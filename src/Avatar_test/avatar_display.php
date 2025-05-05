<?php
session_start();
header('Content-Type: image/png');

if (!isset($_SESSION['user_email'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

$userEmail = $_SESSION['user_email'];

$host = 'localhost';
$dbname = 'user_reg_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT avatar_path FROM avatar WHERE user_id = :user_id");
    $stmt->bindValue(':user_id', $userEmail, PDO::PARAM_STR);
    $stmt->execute();

    $avatarPath = $stmt->fetchColumn();

    if ($avatarPath && file_exists($avatarPath)) {
        readfile($avatarPath);
    } else {
        http_response_code(404);
        echo "Avatar not found";
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database error: " . $e->getMessage();
}
