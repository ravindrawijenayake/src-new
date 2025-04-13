<?php
require_once 'config.php';

header('Content-Type: application/json'); // Set the response type to JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDatabaseConnection();

    // Get form data
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $dateOfBirth = $_POST['dateOfBirth'] ?? '';
    $employment = trim($_POST['employment'] ?? '');
    $email = trim($_POST['signup-email'] ?? '');
    $password = $_POST['signup-password'] ?? '';
    $confirmPassword = $_POST['signup-confirm-password'] ?? '';

    // Validate inputs
    if (empty($firstName) || empty($lastName) || empty($dateOfBirth) || empty($employment) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Invalid email address.']);
        exit;
    }

    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'error' => 'Passwords do not match.']);
        exit;
    }

    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters long.']);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $query = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Email already exists in the database.']);
        exit;
    }

    // Insert the new user
    $query = "INSERT INTO users (first_name, last_name, date_of_birth, employment, email, password)
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssss', $firstName, $lastName, $dateOfBirth, $employment, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User registered successfully!']);
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => 'Error during INSERT: ' . $stmt->error]);
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}
?>