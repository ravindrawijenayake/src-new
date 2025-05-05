from PIL import Image, ImageDraw, ImageOps, ImageFilter
import io
import sys
import json
import mysql.connector
import logging
import os

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

AVATAR_FEATURES = {
    'psychometric': {
        'avoidance': {'palette': 'earth', 'accessories': 'none'},
        'worship': {'palette': 'gold', 'accessories': 'coins'},
        'status': {'palette': 'royal', 'accessories': 'crown'},
        'vigilance': {'palette': 'silver', 'accessories': 'shield'}
    },
    'stage': {
        'first_home': {'background': 'house', 'item': 'keys'},
        'parent': {'background': 'nursery', 'item': 'baby_bottle'},
        'retirement_planning': {'background': 'beach', 'item': 'sunscreen'},
        'retirement': {'background': 'garden', 'item': 'book'},
        'coaching': {'background': 'office', 'item': 'chart'}
    }
}

def apply_features(img, data):
    """Apply visual elements based on psychometric and life stage data"""
    try:
        draw = ImageDraw.Draw(img)
        width, height = img.size

        # Color palettes (RGBA)
        palettes = {
            'earth': (139, 69, 19, 80),    # Brown with transparency
            'gold': (255, 215, 0, 60),     # Gold overlay
            'royal': (65, 105, 225, 60),   # Royal blue
            'silver': (192, 192, 192, 60)  # Silver
        }

        # Background colors
        backgrounds = {
            'house': (173, 216, 230),     # Light blue
            'nursery': (255, 182, 193),   # Pink
            'beach': (255, 248, 220),     # Beach sand
            'garden': (144, 238, 144),    # Light green
            'office': (211, 211, 211)     # Light gray
        }

        # Create background layer
        bg_color = backgrounds.get(data['stage'], (255, 255, 255))
        background = Image.new('RGBA', img.size, bg_color)
        img = Image.alpha_composite(background, img)

        # Add psychometric overlay
        overlay_color = palettes.get(data['category'], (255, 255, 255, 0))
        overlay = Image.new('RGBA', img.size, overlay_color)
        img = Image.alpha_composite(img, overlay)

        # Add accessories
        accessory = AVATAR_FEATURES['psychometric'][data['category']]['accessories']
        if accessory == 'coins':
            draw.ellipse([(width*0.7, height*0.1), (width*0.85, height*0.25)], 
                        fill='gold', outline='darkgoldenrod', width=3)
        elif accessory == 'crown':
            crown_points = [
                (width*0.4, height*0.15),
                (width*0.5, height*0.05),
                (width*0.6, height*0.15),
                (width*0.55, height*0.15),
                (width*0.5, height*0.25),
                (width*0.45, height*0.15)
            ]
            draw.polygon(crown_points, fill='gold', outline='darkgoldenrod')
        elif accessory == 'shield':
            shield_points = [
                (width*0.7, height*0.3),
                (width*0.7, height*0.1),
                (width*0.85, height*0.1),
                (width*0.85, height*0.3),
                (width*0.775, height*0.35)
            ]
            draw.polygon(shield_points, fill='silver', outline='dimgray')

        # Add life stage items
        item = AVATAR_FEATURES['stage'][data['stage']]['item']
        if item == 'keys':
            draw.rectangle([(width*0.1, height*0.7), 
                          (width*0.15, height*0.8)], 
                          fill='saddlebrown')
            draw.ellipse([(width*0.135, height*0.72), 
                         (width*0.145, height*0.78)], 
                         fill='gold')
        elif item == 'baby_bottle':
            draw.rectangle([(width*0.1, height*0.75), 
                           (width*0.13, height*0.8)], 
                           fill='lightpink')
            draw.polygon([(width*0.1, height*0.75),
                         (width*0.115, height*0.73),
                         (width*0.13, height*0.75)], 
                         fill='pink')
        elif item == 'sunscreen':
            draw.ellipse([(width*0.1, height*0.7), 
                         (width*0.2, height*0.8)], 
                         fill='darkorange')
            draw.text((width*0.15, height*0.75), "SPF50", 
                     fill='white', anchor='mm')
        elif item == 'book':
            draw.rectangle([(width*0.1, height*0.7), 
                           (width*0.2, height*0.8)], 
                           fill='navy')
            draw.line([(width*0.15, height*0.7), 
                      (width*0.15, height*0.8)], 
                      fill='gold', width=3)
        elif item == 'chart':
            draw.polygon([(width*0.1, height*0.8),
                         (width*0.15, height*0.7),
                         (width*0.2, height*0.8)], 
                         fill='black')
            draw.line([(width*0.1, height*0.8), 
                      (width*0.15, height*0.7), 
                      (width*0.2, height*0.8)], 
                      fill='white', width=2)

        return img
    except Exception as e:
        logging.error(f"Image processing failed: {str(e)}")
        raise

def generate_avatar(user_email):
    """Generate and store avatar in database"""
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="user_reg_db",
            auth_plugin='mysql_native_password'
        )
        cursor = conn.cursor(dictionary=True)

        # Get user data
        cursor.execute('''
            SELECT p.category, f.stage, i.image_path 
            FROM psychometric_test_responses p
            JOIN future_self_responses f USING (user_email)
            JOIN face_image_responses i USING (user_email)
            WHERE p.user_email = %s
        ''', (user_email,))
        
        result = cursor.fetchone()
        
        if not result:
            raise ValueError(f"No data found for {user_email}")

        # Validate image path
        image_path = result['image_path']
        if not os.path.exists(image_path):
            raise FileNotFoundError(f"Face image not found at {image_path}")

        # Process image
        with Image.open(image_path).convert('RGBA') as base_img:
            base_img = base_img.resize((512, 512))
            processed_img = apply_features(base_img, {
                'category': result['category'],
                'stage': result['stage']
            })

            # Save to buffer
            buffer = io.BytesIO()
            processed_img.save(buffer, format='PNG')
            avatar_blob = buffer.getvalue()

        # Store in database
        cursor.execute('''
            INSERT INTO avatars (user_email, avatar_data)
            VALUES (%s, %s)
            ON DUPLICATE KEY UPDATE avatar_data = VALUES(avatar_data)
        ''', (user_email, avatar_blob))
        
        conn.commit()
        return True

    except Exception as e:
        logging.error(f"Generation failed: {str(e)}")
        raise
    finally:
        if conn.is_connected():
            conn.close()

def generate_avatar(image_path, avatar_path):
    """Generate avatar by applying a filter to the image."""
    img = Image.open(image_path)
    avatar = img.filter(ImageFilter.SMOOTH)
    avatar.save(avatar_path)

def save_to_database(user_id, image_path, avatar_path):
    """Save avatar details to the database."""
    db = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="user_reg_db"
    )
    cursor = db.cursor()
    cursor.execute(
        "INSERT INTO avatar (user_id, image_path, avatar_path) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE avatar_path = %s",
        (user_id, image_path, avatar_path, avatar_path)
    )
    db.commit()
    cursor.close()
    db.close()

if __name__ == '__main__':
    try:
        user_email = sys.argv[1]
        generate_avatar(user_email)
        print(json.dumps({'status': 'ok', 'message': 'Avatar generated'}))
    except Exception as e:
        print(json.dumps({'status': 'error', 'message': str(e)}))
        sys.exit(1)

if __name__ == '__main__':
    user_id = sys.argv[1]
    image_path = sys.argv[2]
    avatar_path = sys.argv[3]

    if not os.path.exists(image_path):
        print("Error: Image file not found")
        sys.exit(1)

    generate_avatar(image_path, avatar_path)
    save_to_database(user_id, image_path, avatar_path)
    print("Avatar generated and saved successfully")
