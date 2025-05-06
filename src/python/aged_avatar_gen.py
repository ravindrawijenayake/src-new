import sys
import os
from PIL import Image, ImageFilter, ImageDraw, ImageEnhance

def generate_avatar(output_path):
    """
    Generate a creative avatar using PIL.
    
    Args:
        output_path (str): Path where the avatar PNG will be saved
        
    Returns:
        None
    """
    try:
        print("Debug: Starting avatar generation...")
        
        # Create a new image with a white background
        size = (400, 400)
        img = Image.new('RGB', size, 'white')
        draw = ImageDraw.Draw(img)
        
        # Draw a simple avatar (circle with face features)
        # Background circle
        draw.ellipse([50, 50, 350, 350], fill='#aabbcc')
        
        # Face features
        draw.ellipse([150, 150, 250, 250], fill='#663399')  # Face
        draw.ellipse([180, 200, 220, 240], fill='white')    # Eyes
        draw.ellipse([280, 200, 320, 240], fill='white')
        draw.arc([150, 250, 250, 300], 0, 180, fill='#ffcc00', width=5)  # Smile
        
        print("Debug: Avatar created successfully.")
        
        # Save the avatar as PNG
        print(f"Debug: Saving avatar as PNG: {output_path}")
        img.save(output_path, 'PNG')
        
        print(f"Debug: Avatar successfully saved to: {output_path}")
        
    except Exception as e:
        print(f"Error during avatar generation: {e}", file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    # Validate command-line arguments
    if len(sys.argv) != 2:
        print("Usage: python generate_avatar.py <output_image_path>")
        sys.exit(1)
    
    output_image_path = sys.argv[1]
    
    # Ensure the output directory exists
    output_dir = os.path.dirname(output_image_path)
    if not os.path.exists(output_dir):
        print(f"Debug: Output directory does not exist. Creating: {output_dir}")
        os.makedirs(output_dir)
    
    # Generate the avatar
    generate_avatar(output_image_path)