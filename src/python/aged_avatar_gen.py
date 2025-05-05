import sys
import os
from PIL import Image, ImageFilter
import python_avatars as avatars
import cairosvg

def generate_avatar(output_path):
    """
    Generate a creative avatar using the python-avatars library.
    
    Args:
        output_path (str): Path where the avatar PNG will be saved
        
    Returns:
        None
    """
    try:
        print("Debug: Starting avatar generation...")
        
        # Create an avatar with valid custom options
        avatar = avatars.Avatar.random(
            background_color="#aabbcc",  # Example background color
            clothing_color="#ffcc00",    # Example clothing color
            hair_color="#663399"         # Example hair color
        )
        
        print("Debug: Avatar created successfully.")
        
        # Save the avatar as an SVG first
        svg_path = output_path.replace(".png", ".svg")
        print(f"Debug: Saving avatar as SVG to: {svg_path}")
        avatar.render(svg_path)
        
        # Convert SVG to PNG
        print(f"Debug: Converting SVG to PNG: {output_path}")
        cairosvg.svg2png(url=svg_path, write_to=output_path)
        
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