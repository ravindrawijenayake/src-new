<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit();
}

$email = trim($_POST['login-email'] ?? '');
$password = $_POST['login-password'] ?? '';

try {
    $stmt = $conn->prepare("SELECT id, password, first_name, last_name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$user = $result->fetch_assoc()) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid email or password']);
        exit();
    }
    
    if (!password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid email or password']);
        exit();
    }
    
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    
    echo json_encode(['success' => true, 'user' => $_SESSION['user_name']]);
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error occurred']);
} finally {
    $stmt->close();
}
?>