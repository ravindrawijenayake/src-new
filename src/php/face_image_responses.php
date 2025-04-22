<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

// Check if the submitted data exists in the session
if (!isset($_SESSION['submitted_stage']) || !isset($_SESSION['submitted_responses'])) {
    header('Location: avatar.php');
    exit;
}

$userEmail = $_SESSION['user_email'];
$userName = $_SESSION['user_name'];
$stage = $_SESSION['submitted_stage'];
$responses = $_SESSION['submitted_responses'];

// Fetch the uploaded face image URL from the database
$host = 'localhost';
$dbname = 'user_reg_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT face_image_url FROM face_image_responses WHERE email = :email ORDER BY id DESC LIMIT 1");
    $stmt->bindValue(':email', $userEmail, PDO::PARAM_STR);
    $stmt->execute();
    $faceImageUrl = $stmt->fetchColumn();
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Your Face Image</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/expenditurestyle.css">
    <link rel="stylesheet" href="../css/futureselfstyle.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>20:20 FC - FINEDICA</h1>
                <p>Expert Financial Coaching</p>
            </div>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="questionnaire.php">Questionnaire</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="avatar.php">Avatar</a></li>
                <li><a href="chatbot.php">Chatbot</a></li>
                <li><a href="logout.php" style="font-size: 14px; color:rgb(7, 249, 168)">Logout <?php echo htmlspecialchars($userName); ?></a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="success-message">Thank you! Your face image has been saved successfully.</div>
        <div class="review-container">
            <h3>Review Your Face Image</h3>
            <?php if ($faceImageUrl): ?>
                <div class="image-preview">
                    <img src="<?php echo htmlspecialchars($faceImageUrl); ?>" alt="Uploaded Face Image" />
                </div>
            <?php else: ?>
                <p>No face image uploaded.</p>
            <?php endif; ?>

            <form action="next_step.php" method="GET" class="next-form">
                <button type="submit" class="next-button">Next</button>
            </form>
        </div>
    </main>
</body>
</html>