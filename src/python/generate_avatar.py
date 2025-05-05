import sys
from PIL import Image, ImageEnhance, ImageFilter, ImageOps, ImageDraw

def add_aging_effects(img):
    """
    Apply aging effects to the image.
    - Desaturate the image to reduce color vibrancy.
    - Add wrinkles or texture.
    - Darken certain areas for an older look.
    """
    # Step 1: Desaturate the image
    enhancer = ImageEnhance.Color(img)
    img = enhancer.enhance(0.5)  # Reduce color saturation

    # Step 2: Add wrinkles (texture overlay)
    width, height = img.size
    texture = Image.new("L", (width, height), 128)  # Create a gray texture
    draw = ImageDraw.Draw(texture)

    # Draw wrinkle-like lines
    for i in range(20, height, 40):
        draw.line([(0, i), (width, i)], fill=10, width=1)

    # Blend the texture with the original image
    img = Image.composite(img, img.filter(ImageFilter.CONTOUR), texture)

    # Step 3: Darken certain areas (e.g., under eyes)
    draw = ImageDraw.Draw(img)
    draw.ellipse([(width * 0.3, height * 0.4), (width * 0.4, height * 0.45)], fill=(50, 50, 50, 128))
    draw.ellipse([(width * 0.6, height * 0.4), (width * 0.7, height * 0.45)], fill=(50, 50, 50, 128))

    return img

def sketch_effect(img):
    """
    Apply a sketch-like effect to the image.
    - Convert the image to grayscale.
    - Invert the colors.
    - Apply a Gaussian blur.
    - Blend the original grayscale image with the blurred inverted image.
    """
    # Convert to grayscale
    gray = img.convert("L")

    # Invert the grayscale image
    inverted = ImageOps.invert(gray)

    # Apply Gaussian blur to the inverted image
    blurred = inverted.filter(ImageFilter.GaussianBlur(10))

    # Blend the grayscale image with the blurred inverted image
    sketch = Image.blend(gray, blurred, alpha=0.5)

    # Convert back to RGB for further processing
    return sketch.convert("RGB")

def add_color_overlay(img, color=(255, 100, 0), intensity=0.3):
    """
    Add a color overlay to the image.
    - `color`: The RGB color of the overlay.
    - `intensity`: The transparency level of the overlay (0 to 1).
    """
    overlay = Image.new("RGB", img.size, color)
    return Image.blend(img, overlay, alpha=intensity)

def cartoonize_with_aging(image_path, output_path):
    """
    Apply cartoonization and aging effects to the image.
    """
    img = Image.open(image_path).convert("RGB")

    # Step 1: Apply smoothing
    img = img.filter(ImageFilter.SMOOTH_MORE)

    # Step 2: Apply aging effects
    img = add_aging_effects(img)

    # Save the final image
    img.save(output_path)

def create_watercolor_avatar(image_path, output_path):
    """
    Create an avatar with a watercolor effect and vignette overlay.
    """
    # Open the input image
    img = Image.open(image_path).convert("RGB")

    # Step 1: Apply the watercolor effect
    img = watercolor_effect(img)

    # Step 2: Add a vignette overlay
    img = add_vignette(img, intensity=0.6)

    # Step 3: Add a border around the avatar
    border_color = (255, 255, 255)  # White border
    border_width = 15
    img = ImageOps.expand(img, border=border_width, fill=border_color)

    # Save the final avatar
    img.save(output_path)

def create_artistic_avatar(image_path, output_path):
    """
    Create an artistic avatar with a sketch effect and color overlay.
    """
    # Open the input image
    img = Image.open(image_path).convert("RGB")

    # Step 1: Apply the sketch effect
    img = sketch_effect(img)

    # Step 2: Add a color overlay (e.g., red tint)
    img = add_color_overlay(img, color=(255, 0, 0), intensity=0.2)

    # Step 3: Add a border around the avatar
    border_color = (0, 0, 0)  # Black border
    border_width = 10
    img = ImageOps.expand(img, border=border_width, fill=border_color)

    # Save the final avatar
    img.save(output_path)

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python generate_avatar.py <input_image> <output_avatar>")
        sys.exit(1)
    
    input_image = sys.argv[1]
    output_avatar = sys.argv[2]

    try:
        create_artistic_avatar(input_image, output_avatar)
        print(f"Avatar saved to {output_avatar}")
    except Exception as e:
        print(f"Error: {e}")
        sys.exit(1)
