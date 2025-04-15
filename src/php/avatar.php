<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$userName = $_SESSION['user_name'];  // Assuming session holds username
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>20:20 FC - FINEDICA</title>
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
                <li><a href="logout.php" style="font-size: 18px; color: Yellow">Logout <?php echo htmlspecialchars($userName); ?></a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="avatar-container">
            <h2>Upload Your Face Image</h2>
            <form action="upload_image.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="face_image" accept="image/*" required>
                <button type="submit" name="upload_face_image">Upload Face Image</button>

                <div class="avatar-section">
            <div class="upload-area">
                <h2>Upload Your Face</h2>
                <input type="file" id="faceUpload" accept="image/*">
                <div class="preview">
                    <!-- Frame for uploaded face image -->
                    <div class="image-frame">
                        <canvas id="faceCanvas"></canvas>
                    </div>
                </div>
                <button id="uploadBtn">Upload Face</button>
            </div>
            <div class="avatar-options">
                <h2>Customize Your Avatar</h2>
                <label for="hairColor">Hair Color:</label>
                <input type="color" id="hairColor" value="#000000">
                <label for="eyeColor">Eye Color:</label>
                <input type="color" id="eyeColor" value="#000000">
                <label for="skinTone">Skin Tone:</label>
                <input type="color" id="skinTone" value="#ffcc99">
                <label for="clothingColor">Clothing Color:</label>
                <input type="color" id="clothingColor" value="#0000ff">
                <button id="generateAvatarBtn">Generate Avatar</button>
            </div>
            <div class="avatar-preview">
                <h2>Your Generated Avatar</h2>
                <!-- Frame for generated avatar -->
                <div class="image-frame">
                    <div id="avatarContainer"></div>
                </div>
            </div>
        </div>
            </form>
            
            <?php if (isset($_SESSION['avatar_url'])): ?>
                <h3>Your Generated Avatar</h3>
                <img src="<?php echo $_SESSION['avatar_url']; ?>" alt="Generated Avatar" width="200">
                <form action="avatar_customization.php" method="POST">
                    <button type="submit" name="customize_avatar">Customize Avatar</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
    <script src="../js/main.js"></script>
    <script src="../js/auth.js"></script>

    <?php
// Logic for avatar customization (e.g., change costume or appearance)
if (isset($_POST['customize_avatar'])) {
    // Customize avatar and update the database
    echo "Avatar customization page. Implement customization logic here!";
}
?>

</html>
