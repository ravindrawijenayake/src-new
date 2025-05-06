from flask import Flask, request, jsonify
import torch
import random
import nltk
from nltk.tokenize import word_tokenize
from model import ChatbotModel
from data_loader import load_intents
import json
from flask_cors import CORS

# Initialize Flask app
app = Flask(__name__)
CORS(app)

# Load intents and model dimensions
vocabulary, intents, intent_responses = load_intents("intents.json")

with open("dimensions.json", "r") as f:
    dimensions = json.load(f)

# Load model
model = ChatbotModel(dimensions['input_size'], dimensions['output_size'])
model.load_state_dict(torch.load("chatbot_model.pth"))
model.eval()

# Helper function for bag of words
def bag_of_words(sentence):
    sentence_words = word_tokenize(sentence)
    bag = [1 if word in sentence_words else 0 for word in vocabulary]
    return torch.tensor([bag], dtype=torch.float32)

# Flask API for chatbot
@app.route('/chat', methods=['POST'])
def chat():
    try:
        data = request.get_json()
        print(f"Debug: Received data: {data}")
        user_message = data.get('message', '')
        if not user_message:
            return jsonify({'error': 'Message is required'}), 400
        bag = bag_of_words(user_message)
        print(f"Debug: Bag of words: {bag}")
        with torch.no_grad():
            output = model(bag)
        _, predicted = torch.max(output, dim=1)
        tag = intents[predicted.item()]
        response = random.choice(intent_responses[tag])
        print(f"Debug: Response: {response}")
        return jsonify({'response': response})
    except Exception as e:
        print(f"Error: {e}")
        return jsonify({'error': 'Internal server error'}), 500

if __name__ == '__main__':
    #app.run(debug=True, host='0.0.0.0', port=80)
    #app.run(debug=True, host='0.0.0.0', port=5001)
    app.run(debug=True, host='0.0.0.0', port=5002)

