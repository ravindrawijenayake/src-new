<?php
session_start();
header('Content-Type: application/json');

// Check if the user is authenticated
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$userEmail = $_SESSION['user_email'];

// Configure paths
$pythonPath = '"C:\\Program Files\\Python311\\python.exe"'; // Ensure the correct Python path
$scriptPath = escapeshellarg('C:/xampp/htdocs/2020FC/src/python/create_avatar.py');

// Build the command
$command = "$pythonPath $scriptPath " . escapeshellarg($userEmail);

// Execute the command with error handling
$descriptors = [
    0 => ['pipe', 'r'], // stdin
    1 => ['pipe', 'w'], // stdout
    2 => ['pipe', 'w']  // stderr
];

$process = proc_open($command, $descriptors, $pipes);

if (!is_resource($process)) {
    error_log("Failed to execute: $command");
    echo json_encode(['status' => 'error', 'message' => 'Process initialization failed']);
    exit;
}

// Capture outputs
$stdout = stream_get_contents($pipes[1]);
$stderr = stream_get_contents($pipes[2]);

// Close pipes
foreach ($pipes as $pipe) {
    fclose($pipe);
}

$returnCode = proc_close($process);

// Handle the output
if ($returnCode !== 0) {
    error_log("Python Error ($returnCode): $stderr");
    echo json_encode([
        'status' => 'error',
        'message' => 'Avatar generation failed',
        'debug' => [
            'command' => $command,
            'error' => $stderr,
            'output' => $stdout
        ]
    ]);
    exit;
}

// Decode the Python script's JSON output
$result = json_decode($stdout, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("Invalid JSON output from Python script: $stdout");
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid response from avatar generation script',
        'debug' => [
            'command' => $command,
            'raw_output' => $stdout
        ]
    ]);
    exit;
}

// Check the Python script's response
if ($result['status'] === 'ok') {
    echo json_encode([
        'status' => 'ok',
        'message' => 'Avatar generated successfully',
        'avatar_url' => "get_avatar.php?email=" . urlencode($userEmail)
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => $result['message'] ?? 'Unknown error occurred',
        'debug' => $result
    ]);
}
