<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Include Google API client library

session_start();


if (!isset($_SESSION['user_name'])) {
    header('Location: index.php'); // Redirect to login if the user is not logged in
    exit;
}

$userName = $_SESSION['user_name']; // Retrieve the username from the session


// Google Drive API setup
function uploadToGoogleDrive($filePath, $folderId) {
    $client = new Google_Client();
    $client->setAuthConfig('C:\xampp\htdocs\2020FC\src\php\credentials.json'); // call credentials file
    $client->addScope(Google_Service_Drive::DRIVE_FILE);

    $service = new Google_Service_Drive($client);

    $file = new Google_Service_Drive_DriveFile();
    $file->setName(basename($filePath));
    $file->setParents([$folderId]);

    $content = file_get_contents($filePath);

    $uploadedFile = $service->files->create($file, [
        'data' => $content,
        'mimeType' => mime_content_type($filePath),
        'uploadType' => 'multipart'
    ]);

    return $uploadedFile->getWebViewLink();
}


// Update database logic to match the correct database and table structure


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['faceImage'])) {
        // Log detailed file upload information
        error_log('File upload details: ' . print_r($_FILES['faceImage'], true));

        if ($_FILES['faceImage']['error'] !== UPLOAD_ERR_OK) {
            error_log('File upload error: ' . $_FILES['faceImage']['error']);
            echo json_encode(['success' => false, 'message' => 'File upload error.']);
            exit;
        }

        $uploadDir = __DIR__ . '/../uploads/'; // Ensure absolute path
        $fileName = basename($_FILES['faceImage']['name']);
        $filePath = $uploadDir . $fileName;

        // Log the file upload attempt
        error_log('Attempting to upload file: ' . $fileName);

        if (!is_dir($uploadDir)) {
            error_log('Uploads directory does not exist. Creating it.');
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($_FILES['faceImage']['tmp_name'], $filePath)) {
            error_log('File uploaded successfully to: ' . $filePath);
            try {
                // Upload to Google Drive
                $googleDriveFolderId = '1_os5zlO3EgFXtIQGMTVG4b2f52oQGXfh';
                $faceImageUrl = uploadToGoogleDrive($filePath, $googleDriveFolderId);

                // Log session user_name
                if (!isset($userName)) {
                    error_log('Session user_name is not set.');
                    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
                    exit;
                }
                error_log('Session user_name: ' . $userName);

                // Save to database
                $pdo = new PDO('mysql:host=localhost;dbname=user_reg_db', 'root', '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // Use user_email session variable for database query
                $stmt = $pdo->prepare('UPDATE users SET face_image = ? WHERE email = ?');
                $stmt->execute([$faceImageUrl, $_SESSION['user_email']]);

                error_log('Database updated successfully with face image URL.');
                echo json_encode(['success' => true, 'faceImageUrl' => $faceImageUrl]);
            } catch (Exception $e) {
                error_log('Database error: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Database error.']);
            }
        } else {
            error_log('Failed to move uploaded file.');
            echo json_encode(['success' => false, 'message' => 'Failed to upload face image.']);
        }
    } elseif (isset($_POST['avatarData'])) {
        $avatarData = $_POST['avatarData'];
        $avatarFilePath = __DIR__ . '/../uploads/' . $userName . '_avatar.png';

        try {
            // Save avatar image
            file_put_contents($avatarFilePath, base64_decode($avatarData));
            error_log('Avatar image saved to: ' . $avatarFilePath);

            // Upload to Google Drive
            $googleDriveFolderId = '1eoBhHN9DUFImlFJpXlOcxWXZ7tCro0ku';
            $avatarImageUrl = uploadToGoogleDrive($avatarFilePath, $googleDriveFolderId);

            // Save to database
            $pdo = new PDO('mysql:host=localhost;dbname=user_reg_db', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare('UPDATE users SET avatar_image = ? WHERE user_name = ?');
            $stmt->execute([$avatarImageUrl, $userName]);

            error_log('Database updated successfully with avatar image URL.');
            echo json_encode(['success' => true, 'avatarImageUrl' => $avatarImageUrl]);
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error.']);
        }
    }
}
?>

<!-- src/html/index.html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>20:20 FC- FINEDICA</title>
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
                <li><a href="logout.php" style="font-size: 18px; color: Yellow">Logout <?php echo htmlspecialchars($userName);?></a></li>
            </ul>
        </nav>
    </header>
    <main>
        <!-- Your website content goes here -->
         <!-- src/html/avatar.html -->
<div class="avatar-section">
    <div class="upload-area">
        <h2>Upload Your Face</h2>
        <input type="file" id="faceUpload" accept="image/*">
        <div class="preview">
            <!-- Frame for uploaded face image -->
            <div class="image-frame">
            <input type="file" id="faceUpload" accept="image/*">
            </div>
        </div>
        <button id="uploadBtn">Upload Face <input type="file" id="faceUpload" accept="image/*"></button>
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
    </main>
    <script src="../js/main.js"></script>
</body>
</html>