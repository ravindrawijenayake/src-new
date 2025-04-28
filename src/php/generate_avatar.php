<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'user_reg_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Read input
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Email is required']);
    exit;
}

$email = $data['email'];

// Get face image URL
$stmt = $pdo->prepare("SELECT face_image_url FROM face_image_responses WHERE email = :email ORDER BY id DESC LIMIT 1");
$stmt->bindParam(':email', $email);
$stmt->execute();
$faceImageUrl = $stmt->fetchColumn();

if (!$faceImageUrl) {
    echo json_encode(['status' => 'error', 'message' => 'No face image found']);
    exit;
}

// Prepare paths
$source_image = realpath(__DIR__ . "/../uploads/" . basename($faceImageUrl));
$avatar_output_dir = realpath(__DIR__ . "/../avatars/");
$avatar_output = $avatar_output_dir . "/avatar_" . basename($faceImageUrl);

if (!$source_image || !$avatar_output_dir) {
    echo json_encode(['status' => 'error', 'message' => 'File path error']);
    exit;
}

// Build and run Python command
$python = "C:\\Users\\Administrator\\AppData\\Local\\Microsoft\\WindowsApps\\python.exe";
$script = realpath(__DIR__ . "/../python/generate_avatar.py");

$cmd = "\"$python\" \"$script\" \"$source_image\" \"$avatar_output\"";
exec($cmd, $output, $return_var);

if ($return_var !== 0) {
    echo json_encode(['status' => 'error', 'message' => 'Avatar generation failed']);
    exit;
}

// Check if avatar was created
if (!file_exists($avatar_output)) {
    echo json_encode(['status' => 'error', 'message' => 'Avatar file not found']);
    exit;
}

// Save avatar path to the database
try {
    $stmt = $pdo->prepare("
        INSERT INTO avatar (email, avatar_path) 
        VALUES (:email, :avatar_path) 
        ON DUPLICATE KEY UPDATE avatar_path = :avatar_path
    ");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':avatar_path', $avatar_output);
    $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save avatar to database: ' . $e->getMessage()]);
    exit;
}

// Return success response
$avatar_web_path = "/2020FC/src/avatars/" . basename($avatar_output);
echo json_encode(['status' => 'ok', 'avatar_path' => $avatar_web_path]);
exit;
?>
