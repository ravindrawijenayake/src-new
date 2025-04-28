<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$userEmail = $_SESSION['user_email'];

// Configure paths
$pythonPath = '"C:\\Program Files\\Python313\\python.exe"';
$scriptPath = escapeshellarg('C:/xampp/htdocs/2020FC/src/python/create_avatar.py');

// Build command
$command = "$pythonPath $scriptPath " . escapeshellarg($userEmail);

// Execute with error handling
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

// Get outputs
$stdout = stream_get_contents($pipes[1]);
$stderr = stream_get_contents($pipes[2]);

foreach ($pipes as $pipe) {
    fclose($pipe);
}

$returnCode = proc_close($process);

// Handle output
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

$result = json_decode($stdout, true);

if ($result['status'] === 'ok') {
    echo json_encode([
        'status' => 'ok',
        'message' => 'Avatar generated successfully',
        'avatar_url' => "get_avatar.php?email=" . urlencode($userEmail)
    ]);
} else {
    echo json_encode($result);
}
