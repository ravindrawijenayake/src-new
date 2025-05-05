from flask import Flask, request, jsonify
import os
from PIL import Image, ImageFilter
import mysql.connector
from datetime import datetime
import logging

app = Flask(__name__)
UPLOAD_FOLDER = 'uploads'
AVATAR_FOLDER = 'avatars'
os.makedirs(UPLOAD_FOLDER, exist_ok=True)
os.makedirs(AVATAR_FOLDER, exist_ok=True)

app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
app.config['AVATAR_FOLDER'] = AVATAR_FOLDER

logging.basicConfig(level=logging.DEBUG)

def generate_avatar(image_path, avatar_path):
    """Generate avatar by applying a filter to the image."""
    img = Image.open(image_path)
    avatar = img.filter(ImageFilter.SMOOTH)
    avatar.save(avatar_path)

@app.route('/upload', methods=['POST'])
def upload_image():
    logging.debug("Upload endpoint hit")
    if 'file' not in request.files:
        logging.error("No file part in the request")
        return jsonify({'error': 'No file part'}), 400
    file = request.files['file']
    user_id = request.form.get('user_id')

    if file.filename == '':
        logging.error("No selected file")
        return jsonify({'error': 'No selected file'}), 400

    filename = f"{datetime.now().strftime('%Y%m%d%H%M%S')}_{file.filename}"
    upload_path = os.path.join(app.config['UPLOAD_FOLDER'], filename)
    file.save(upload_path)

    avatar_filename = f"avatar_{filename}"
    avatar_path = os.path.join(app.config['AVATAR_FOLDER'], avatar_filename)

    logging.debug(f"Generating avatar for {user_id} at {avatar_path}")
    generate_avatar(upload_path, avatar_path)

    # Insert record into database
    db = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="user_reg_db"
    )
    cursor = db.cursor()
    cursor.execute(
        "INSERT INTO avatar (user_id, image_path, avatar_path) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE avatar_path = %s",
        (user_id, upload_path, avatar_path, avatar_path)
    )
    db.commit()
    cursor.close()
    db.close()

    logging.debug(f"Avatar created and saved at {avatar_path}")
    return jsonify({'message': 'File uploaded and avatar created', 'avatar_path': avatar_path})

if __name__ == '__main__':
    app.run(port=5000, debug=True)
