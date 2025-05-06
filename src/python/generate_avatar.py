import sys
from PIL import Image, ImageEnhance, ImageFilter, ImageOps, ImageDraw, ImageChops
import numpy as np

def resize_and_crop(img, target_size=(512, 512)):
    """
    Resize and crop image to create a perfect square avatar.
    """
    width, height = img.size
    size = min(width, height)
    left = (width - size) // 2
    top = (height - size) // 2
    right = left + size
    bottom = top + size
    
    img = img.crop((left, top, right, bottom))
    return img.resize(target_size, Image.Resampling.LANCZOS)

def add_avatar_effects(img):
    """
    Apply avatar-specific effects to make the image look more like an avatar.
    """
    # Convert to RGBA if not already
    img = img.convert('RGBA')
    
    # Create a mask for the circular shape
    mask = Image.new('L', img.size, 0)
    draw = ImageDraw.Draw(mask)
    draw.ellipse((0, 0, img.size[0], img.size[1]), fill=255)
    
    # Apply the mask
    output = Image.new('RGBA', img.size, (0, 0, 0, 0))
    output.paste(img, mask=mask)
    
    # Add a subtle glow effect
    glow = output.filter(ImageFilter.GaussianBlur(radius=2))
    output = ImageChops.add(output, glow)
    
    return output

def enhance_features(img):
    """
    Enhance facial features to make them more prominent.
    """
    # Enhance contrast
    enhancer = ImageEnhance.Contrast(img)
    img = enhancer.enhance(1.2)
    
    # Enhance sharpness
    enhancer = ImageEnhance.Sharpness(img)
    img = enhancer.enhance(1.3)
    
    # Enhance color
    enhancer = ImageEnhance.Color(img)
    img = enhancer.enhance(1.1)
    
    return img

def add_avatar_style(img):
    """
    Apply artistic styling to make it look more like an avatar.
    """
    # Apply edge enhancement
    img = img.filter(ImageFilter.EDGE_ENHANCE_MORE)
    
    # Add a subtle cartoon effect
    img = img.filter(ImageFilter.SMOOTH_MORE)
    
    # Add a slight vignette effect
    width, height = img.size
    mask = Image.new('L', (width, height), 255)
    draw = ImageDraw.Draw(mask)
    draw.ellipse([-width/4, -height/4, width*1.25, height*1.25], fill=0)
    mask = mask.filter(ImageFilter.GaussianBlur(radius=width/8))
    img.putalpha(mask)
    
    return img

def create_avatar(image_path, output_path):
    """
    Create a polished avatar from the input image.
    """
    try:
        # Open and convert image
        img = Image.open(image_path).convert('RGB')
        
        # Resize and crop to square
        img = resize_and_crop(img)
        
        # Enhance features
        img = enhance_features(img)
        
        # Apply avatar styling
        img = add_avatar_style(img)
        
        # Add avatar effects
        img = add_avatar_effects(img)
        
        # Save the final avatar
        img.save(output_path, 'PNG', quality=95)
        return True
        
    except Exception as e:
        print(f"Error creating avatar: {str(e)}", file=sys.stderr)
        return False

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python generate_avatar.py <input_image> <output_avatar>")
        sys.exit(1)
    
    input_image = sys.argv[1]
    output_avatar = sys.argv[2]

    success = create_avatar(input_image, output_avatar)
    if not success:
        sys.exit(1)
    print(f"Avatar saved to {output_avatar}")
