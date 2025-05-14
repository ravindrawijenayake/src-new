<?php
session_start();
header("Content-Type: application/json");

// Database connection
$host = 'localhost';
$dbname = 'user_reg_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $_SESSION['user_email'] ?? null;
    $responses = $data['responses'] ?? null;

    // Debugging logs
    error_log("Received email: " . $email);
    error_log("Received responses: " . print_r($responses, true));

    if (!$email || !$responses) {
        echo json_encode(["success" => false, "error" => "Missing email or responses."]);
        exit;
    }

    // Calculate scores for each category
    $category_scores = [];
    foreach ($responses as $category => $answers) {
        $category_scores[$category] = array_sum($answers);
    }

    // Find dominant belief
    $dominant_belief = array_keys($category_scores, max($category_scores))[0];

    try {
        $stmt = $pdo->prepare("INSERT INTO psychometric_test_responses 
            (email, dominant_belief, money_avoidance, money_worship, money_status, money_vigilance, responses)
            VALUES (:email, :dominant_belief, :money_avoidance, :money_worship, :money_status, :money_vigilance, :responses)
            ON DUPLICATE KEY UPDATE
                dominant_belief = VALUES(dominant_belief),
                money_avoidance = VALUES(money_avoidance),
                money_worship = VALUES(money_worship),
                money_status = VALUES(money_status),
                money_vigilance = VALUES(money_vigilance),
                responses = VALUES(responses),
                updated_at = CURRENT_TIMESTAMP");

        $stmt->execute([
            ':email' => $email,
            ':dominant_belief' => $dominant_belief,
            ':money_avoidance' => $category_scores['Money Avoidance'] ?? 0,
            ':money_worship' => $category_scores['Money Worship'] ?? 0,
            ':money_status' => $category_scores['Money Status'] ?? 0,
            ':money_vigilance' => $category_scores['Money Vigilance'] ?? 0,
            ':responses' => json_encode($responses)
        ]);

        echo json_encode([
            'success' => true,
            'scores' => $category_scores,
            'dominant_belief' => $dominant_belief
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
    exit;
}
?>
