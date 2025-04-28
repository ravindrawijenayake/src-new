<?php
session_start();
require_once __DIR__ . '/../src/php/config.php';

if (!isset($_GET['email']) || !filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Invalid email address');
}

try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare('SELECT avatar_data FROM avatars WHERE user_email = ?');
    $stmt->execute([$_GET['email']]);
    $avatarData = $stmt->fetchColumn();

    if ($avatarData) {
        header('Content-Type: image/png');
        echo $avatarData;
    } else {
        http_response_code(404);
        echo 'Avatar not found';
    }
} catch (PDOException $e) {
    http_response_code(500);
    error_log("Database error: " . $e->getMessage());
    echo 'Error retrieving avatar';
}
