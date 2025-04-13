<!-- src/html/index.html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cool Website</title>
    <link rel="stylesheet" href="../css/main.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>20:20 FC - FINEDICA</h1>
                <p>Expert Financial Coaching</p>
            </div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="questionnaire.php">Questionnaire</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="avatar.php">Avatar</a></li>
                <li><a href="chatbot.php">Chatbot</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <!-- Your website content goes here -->
         <!-- src/html/avatar.html -->
<div class="avatar-section">
    <div class="upload-area">
        <input type="file" id="faceUpload" accept="image/*">
        <div class="preview">
            <canvas id="faceCanvas"></canvas>
        </div>
    </div>
    <div class="avatar-preview">
        <h2>Your Generated Avatar</h2>
        <div id="avatarContainer"></div>
    </div>
</div>
    </main>
    <script src="../js/main.js"></script>
</body>
</html>