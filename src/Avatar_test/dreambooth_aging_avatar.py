import requests
import base64
import sys
import os
from PIL import Image
import io

# Hugging Face API Token (replace YOUR_TOKEN_HERE with your real token)
API_TOKEN = "YOUR_HUGGINGFACE_API_TOKEN"
API_URL = "https://api-inference.huggingface.co/models/stabilityai/stable-diffusion-2-1"  # or your Dreambooth fine-tuned model

headers = {
    "Authorization": f"Bearer {API_TOKEN}"
}

def query(payload):
    response = requests.post(API_URL, headers=headers, json=payload)
    if response.status_code != 200:
        raise Exception(f"Failed to call API: {response.text}")
    return response.content

def dreambooth_cartoon_aging(input_path, output_path):
    # Load the input image and encode it to base64
    with open(input_path, "rb") as image_file:
        encoded_image = base64.b64encode(image_file.read()).decode('utf-8')

    # Prompt: you can customize this prompt for better effects
    prompt = "Create a cartoon-style portrait of this person as a 60-year-old, artistic style, vibrant, detailed, soft lighting."

    # Send request to Hugging Face Dreambooth API
    payload = {
        "inputs": prompt,
        "options": {"wait_for_model": True},
        "parameters": {
            "image": encoded_image,
            "guidance_scale": 8,
            "num_inference_steps": 30
        }
    }

    print("Sending image to Dreambooth model... please wait.")
    output = query(payload)

    # Save the resulting image
    image = Image.open(io.BytesIO(output))
    image.save(output_path, format="JPEG")
    print(f"Artistic aged avatar saved to {output_path}")

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python dreambooth_aging_avatar.py <input_image> <output_avatar>")
        sys.exit(1)

    input_image = sys.argv[1]
    output_avatar = sys.argv[2]
    dreambooth_cartoon_aging(input_image, output_avatar)
