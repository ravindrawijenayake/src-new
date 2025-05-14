from flask import Flask, request, jsonify
import torch
import random
import nltk
from nltk.tokenize import word_tokenize
from model import ChatbotModel
from data_loader import load_intents
import json
from flask_cors import CORS
from nltk.corpus import stopwords
import string
from sklearn.feature_extraction.text import TfidfVectorizer
import requests
from pathlib import Path

nltk.download('stopwords')
stop_words = set(stopwords.words('english'))

# Initialize Flask app
app = Flask(__name__)
CORS(app)

# Load intents and model dimensions
vocabulary, intents, intent_responses = load_intents("intents.json")

with open("dimensions.json", "r") as f:
    dimensions = json.load(f)

with open("tfidf_vocab.json", "r") as f:
    tfidf_vocab = json.load(f)

# Load model
model = ChatbotModel(dimensions['input_size'], dimensions['output_size'])
model.load_state_dict(torch.load("chatbot_model.pth"))
model.eval()

# Load and fit TF-IDF vectorizer
vectorizer = TfidfVectorizer(vocabulary=tfidf_vocab)
vectorizer.fit([" ".join(patterns) for patterns in intent_responses.values()])  # Ensure fitting happens here

STABILITY_API_KEY = 'sk-hV3rJIrVaxzsLiq0FwEQ9RNCYBwvm1NcMXwkYhfpUuABSnds'  # Replace with your actual key
STABILITY_URL = 'https://api.stability.ai/v2beta/stable-image/generate/core'

def generate_stability_image(prompt, user_gender=None):
    # Optionally prepend gender to prompt
    if user_gender:
        prompt = f"{user_gender}. {prompt}"
    boundary = '----WebKitFormBoundary7MA4YWxkTrZu0gW'
    multipart_data = (
        f'--{boundary}\r\n'
        f'Content-Disposition: form-data; name="prompt"\r\n\r\n{prompt}\r\n'
        f'--{boundary}\r\n'
        f'Content-Disposition: form-data; name="output_format"\r\n\r\npng\r\n'
        f'--{boundary}--\r\n'
    )
    headers = {
        'Authorization': f'Bearer {STABILITY_API_KEY}',
        'Content-Type': f'multipart/form-data; boundary={boundary}',
        'Accept': 'image/png'
    }
    response = requests.post(STABILITY_URL, data=multipart_data, headers=headers)
    if response.status_code == 200:
        # Save image to a file and return the path (or return the image bytes)
        filename = f"generated_chatbot_avatar.png"
        with open(filename, 'wb') as f:
            f.write(response.content)
        return filename
    else:
        return None

def preprocess_text(sentence):
    """
    Preprocess the input sentence by tokenizing, removing punctuation, and stopwords.
    """
    sentence = sentence.lower()
    tokens = nltk.word_tokenize(sentence)
    tokens = [word for word in tokens if word not in string.punctuation and word not in stop_words]
    return " ".join(tokens)

import requests

def call_ollama(prompt, model='phi3'):
    response = requests.post(
        'http://localhost:11434/api/generate',
        json={'model': model, 'prompt': prompt, 'stream': False, 'options': {'num_predict': 64}}
    )
    if response.ok:
        return response.json().get('response', '').strip()
    return "Sorry, I couldn't get a response from the LLM."

# === RAG (Retrieval-Augmented Generation) Setup ===
try:
    from llama_index.core import SimpleDirectoryReader, VectorStoreIndex
    from llama_index.core.node_parser import SimpleNodeParser
    from llama_index.core.query_engine import RetrieverQueryEngine
    from llama_index.core.retrievers import VectorIndexRetriever
    from llama_index.embeddings.huggingface import HuggingFaceEmbedding
    # Use a small, local embedding model (e.g., BAAI/bge-small-en-v1.5)
    embed_model = HuggingFaceEmbedding(model_name="BAAI/bge-small-en-v1.5")
    documents = SimpleDirectoryReader('../data', recursive=True).load_data()
    print(f"Loaded {len(documents)} documents for RAG.")
    for doc in documents:
        print(f"Document metadata: {getattr(doc, 'metadata', {})}")
        print(f"Document preview: {getattr(doc, 'text', '')[:200]}")
    index = VectorStoreIndex.from_documents(documents, embed_model=embed_model)
    retriever = VectorIndexRetriever(index=index, similarity_top_k=2)
    rag_query_engine = RetrieverQueryEngine(retriever=retriever)
    rag_enabled = True
except Exception as e:
    print(f"RAG setup failed: {e}")
    rag_enabled = False

# Flask API for chatbot
@app.route('/chat', methods=['POST'])
def chat():
    try:
        data = request.get_json()
        user_message = data.get('message', '').strip().lower()
        user_gender = data.get('gender', '').strip().capitalize() if data.get('gender') else None

        # Use intent model for avatar/image requests
        if 'generate avatar' in user_message or 'show me my avatar' in user_message:
            prompt = f"A professional avatar, photorealistic, {user_gender if user_gender else ''}"
            image_path = generate_stability_image(prompt, user_gender)
            if image_path:
                rel_path = Path(image_path).name
                return jsonify({'response': f"Here is your generated avatar:", 'image': rel_path})
            else:
                return jsonify({'response': "Sorry, I couldn't generate an image right now."})

        # === RAG: Try to answer using your own documents ===
        if rag_enabled:
            try:
                from llama_index.core.retrievers import VectorIndexRetriever
                retriever = VectorIndexRetriever(index=index, similarity_top_k=5)
                nodes = retriever.retrieve(user_message)
                print("RAG retrieved nodes:", nodes)
                if nodes:
                    # Concatenate top 3 chunks for more context
                    context = "\n\n".join([node.get_content().strip() for node in nodes[:3]])
                    prompt = f"Context: {context}\n\nUser question: {user_message}\n\nAnswer:"
                    llm_response = call_ollama(prompt, model='phi3')
                    print("Ollama LLM response (with RAG context):", llm_response)
                    if llm_response:
                        return jsonify({'response': llm_response})
                else:
                    print("RAG: No relevant nodes, will fallback to LLM.")
            except Exception as rag_e:
                print(f"RAG error: {rag_e}")
                # fallback to LLM

        # Otherwise, use Ollama LLM for chat
        print("Falling back to Ollama LLM...")
        prompt = user_message
        # Only add gender if the question is about gender/appearance/avatar
        gender_keywords = ['gender', 'appearance', 'avatar', 'man', 'woman', 'male', 'female']
        if user_gender in ['Male', 'Female'] and any(word in user_message for word in gender_keywords):
            prompt = f"{user_gender}: {user_message}"
        llm_response = call_ollama(prompt, model='phi3')
        print("Ollama LLM response:", llm_response)
        if not llm_response:
            llm_response = "Sorry, I couldn't get a response from the LLM."
        return jsonify({'response': llm_response})

    except Exception as e:
        print(f"Error: {e}")
        return jsonify({'response': f"An error occurred: {str(e)}"}), 500

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5002)