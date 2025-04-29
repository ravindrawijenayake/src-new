<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit();
}

// Validate input data
$firstName = trim($_POST['firstName'] ?? '');
$lastName = trim($_POST['lastName'] ?? '');
$dateOfBirth = $_POST['dateOfBirth'] ?? '';
$employment = trim($_POST['employment'] ?? '');
$email = trim($_POST['signup-email'] ?? '');
$password = $_POST['signup-password'] ?? '';
$confirmPassword = $_POST['signup-confirm-password'] ?? '';

// Validate password match
if ($password !== $confirmPassword) {
    http_response_code(400);
    echo json_encode(['error' => 'Passwords do not match']);
    exit();
}

// Hash password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if email exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Email already exists']);
        exit();
    }
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, date_of_birth, employment, email, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstName, $lastName, $dateOfBirth, $employment, $email, $passwordHash);
    
    if (!$stmt->execute()) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    echo json_encode(['success' => true]);
    exit();
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error occurred']);
} finally {
    $stmt->close();
}
?>