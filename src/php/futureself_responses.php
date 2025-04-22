<?php
session_start();

if (!isset($_SESSION['submitted_stage']) || !isset($_SESSION['submitted_responses'])) {
    header('Location: futureself.php');
    exit;
}

$stage = $_SESSION['submitted_stage'];
$responses = $_SESSION['submitted_responses'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Your Responses</title>
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
                <li><a href="logout.php" style="font-size: 14px; color:rgb(7, 249, 168)">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="success-message">Thank you! Your responses have been saved successfully.</div>
        <div class="review-container">
            <h3>Review Your Responses</h3>
            <p><strong>Stage of Life:</strong> <?php echo htmlspecialchars($stage); ?></p>

            <?php foreach ($responses as $category => $questions): ?>
                <div class="category-container">
                    <h4><?php echo htmlspecialchars($category); ?></h4>
                    <?php foreach ($questions as $question => $response): ?>
                        <p><strong><?php echo htmlspecialchars($question); ?></strong><br><?php echo htmlspecialchars($response); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <form action="avatar.php" method="GET" class="next-form">
                <button type="submit" class="next-button">Next</button>
            </form>
        </div>
    </main>
</body>
</html>