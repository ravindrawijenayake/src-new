<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

$userEmail = $_SESSION['user_email']; // Use email as the user identifier

// Database connection
$conn = new mysqli('localhost', 'root', '', 'users'); // Update with your DB credentials

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle file uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profileImagePath = "../uploads/" . basename($_FILES['profileImage']['name']);
    $generatedAvatarPath = "../uploads/" . basename($_FILES['generatedAvatar']['name']);

    // Move uploaded files to the uploads directory
    move_uploaded_file($_FILES['profileImage']['tmp_name'], $profileImagePath);
    move_uploaded_file($_FILES['generatedAvatar']['tmp_name'], $generatedAvatarPath);

    // Update the database with the new image paths
    $sql = "UPDATE users SET profile_image_url = ?, generated_avatar_url = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $profileImagePath, $generatedAvatarPath, $userEmail);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header('Location: chatbot.php'); // Redirect back to the chatbot page
    exit;
}
?>