<!-- filepath: c:\xampp\htdocs\2020FC\src\php\futureself.php -->
<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

$userEmail = $_SESSION['user_email'];
$userName = $_SESSION['user_name'];

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost';
$dbname = 'user_reg_db'; // Use the user registration database
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if user already has a future self response
$stmt = $pdo->prepare("SELECT stage, category, question, response FROM future_self_responses WHERE email = :email");
$stmt->execute([':email' => $userEmail]);
$existing_responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stage = $_POST['stage'];
    $responses = $_POST['responses'];

    $stmt = $pdo->prepare("INSERT INTO future_self_responses (email, stage, category, question, response) VALUES (:email, :stage, :category, :question, :response)");

    foreach ($responses as $category => $questions) {
        foreach ($questions as $question => $response) {
            $stmt->bindValue(':email', $userEmail, PDO::PARAM_STR);
            $stmt->bindValue(':stage', $stage, PDO::PARAM_STR);
            $stmt->bindValue(':category', $category, PDO::PARAM_STR);
            $stmt->bindValue(':question', $question, PDO::PARAM_STR);
            $stmt->bindValue(':response', $response, PDO::PARAM_STR);

            // Execute the query and check for errors
            if (!$stmt->execute()) {
                echo "<div class='error-message'>Error inserting data: " . htmlspecialchars(implode(", ", $stmt->errorInfo())) . "</div>";
                exit;
            }
        }
    }

    // Store the submitted data in the session for review
    $_SESSION['submitted_stage'] = $stage;
    $_SESSION['submitted_responses'] = $responses;

    // Redirect to the review page
    header('Location: futureself_responses.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>20:20 FC - FINEDICA</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/expenditurestyle.css">
    <link rel="stylesheet" href="../css/futureselfstyle.css">
    <link rel="stylesheet" href="../css/progressbar.css">
    <link rel="stylesheet" href="future_self_style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>20:20 FC - FINEDICA</h1>
                <p>Expert Financial Coaching</p>
            </div>
            <ul>
                <li><a href="../php/home.php">Home</a></li>
                <li><a href="../php/questionnaire.php">Questionnaire</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="avatar.php">Avatar</a></li>
                <li><a href="../chatbot/chatbot.php">Chatbot</a></li>
                <li><a href="../php/logout.php" style="font-size: 14px; color:rgb(7, 249, 168)">Logout <?php echo htmlspecialchars($userName); ?></a></li>
            </ul>
        </nav>
    </header>
    <?php $progressStep = 2; include '../php/progressbar.php'; ?>
    <main>
        <div class="futureself-hero">
            <h1><span class="icon">🌱</span> Imagine Your Future Self</h1>
            <p class="subtitle">Select a stage of life and answer the questions to envision your future.<br> Your answers help us create a personalized avatar and financial plan for you!</p>
        </div>
        <?php if ($existing_responses && count($existing_responses) > 0): ?>
            <div class="future-self-results card">
                <h2><span class="icon">📋</span> Your Previous Future Self Responses</h2>
                <?php
                // Group responses by stage and category
                $stage = $existing_responses[0]['stage'] ?? '';
                $grouped = [];
                foreach ($existing_responses as $resp) {
                    $grouped[$resp['category']][] = [
                        'question' => $resp['question'],
                        'response' => $resp['response']
                    ];
                }
                ?>
                <div class="stage-selected"><strong>Stage of Life:</strong> <?php echo htmlspecialchars($stage); ?></div>
                <?php foreach ($grouped as $category => $qas): ?>
                    <div class="category-container">
                        <h4 class="category-title"><?php echo htmlspecialchars($category); ?></h4>
                        <ul class="qa-list">
                            <?php foreach ($qas as $qa): ?>
                                <li>
                                    <span class="question-text"><?php echo htmlspecialchars($qa['question']); ?></span>
                                    <span class="answer-text"><?php echo htmlspecialchars($qa['response']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
                <div class="nav-buttons-row">
                    <button id="back-btn" class="nav-btn nav-btn-left" onclick="window.history.back();return false;">Back</button>
                    <button id="next-btn" class="nav-btn nav-btn-right">Next</button>
                </div>
            </div>
        <?php else: ?>
        <form action="futureself.php" method="POST" class="futureself-form card">
            <fieldset>
                <legend><span class="icon">🎯</span> Select a Stage of Life</legend>
                <label><input type="radio" name="stage" value="Buying your first home" required> Age 18-30: Buying your first home</label><br>
                <label><input type="radio" name="stage" value="Becoming a Parent" required> Age 25-35: Becoming a Parent</label><br>
                <label><input type="radio" name="stage" value="Planning Retirement" required> Age 35-50: Planning Retirement</label><br>
                <label><input type="radio" name="stage" value="Retirement" required> Age 60+: Retirement</label><br>
                <label><input type="radio" name="stage" value="General Financial Coaching" required> General Financial Coaching</label>
            </fieldset>
            <fieldset>
                <legend><span class="icon">📝</span> Questions</legend>
                <?php
                // Define questions for each category
                $categories = [
                    "Physicality" => [
                        "1. How old will you be when you achieve your financial goal?",
                        "2. What colour will your hair be?",
                        "3.	How will your posture exude confidence and satisfaction in achieving a major financial milestone?",
                        "4.	How will you dress?",
                        "5.	What standard of clothes will you buy?",
                        "6.	Do you exercise?"
                    ],
                    "Income and Personal Finances" => [
                        "1.	How much money will you earn?",
                        "2.	What is your most valuable financial asset (house, etc)?",
                        "3.	Will you have a mortgage?",
                        "4.	What investments will you have?",
                        "5.	How will you have saved?",
                        "6.	How much disposable income will you have at this time?" 
                    ],
                    "Emotional/ Spiritual Values" => [
                        "1.	What will achieving this goal do for you as a person? For instance, if you sacrifice to get on the property ladder, will you have a sense of pride and achievement?",
                        "2.	What are your core values?",
                        "3.	How will you keep yourself emotionally balanced and healthy?",
                        "4.	How do your core values relate to your financial goal?"
                    ],
                    "LifeStyle" => [
                        "1.	What will your hobbies and interests be?",
                        "2.	How many holidays a year will you go on?",
                        "3.	What will you have to sacrifice to achieve your financial goals?",
                        "4.	How can you reframe these sacrifices? ",
                        "5.	How do you balance your lifestyle against achieving and maintaining your financial goals?"
                    ],
                    "Profession" => [
                        "1.	What do you do for work?",
                        "2.	How much do you earn?",
                        "3.	Have you gained a promotion to achieve your financial goals?",
                        "4.	What has your career trajectory been?",
                        "5.	How did you motivate yourself to stay the course to achieve your financial goals?",
                        "6.	How did you maintain a good work-life balance?"
                    ],
                    "Relationships" => [
                        "1.	Do you have family?",
                        "2.	Who are your friends?",
                        "3.	What do you do with your friends?",
                        "4.	Do your relationships support you in achieving your financial goals?",
                        "5.	What financial boundaries will you need to set and maintain to achieve your financial goals?",
                        "6.	What type of people do you associate with?"
                    ]
                ];

                // Generate the form dynamically
                foreach ($categories as $category => $questions) {
                    echo "<fieldset>";
                    echo "<legend>$category</legend>";
                    foreach ($questions as $question) {
                        echo "<label>$question</label><br>";
                        echo "<textarea name='responses[$category][$question]' rows='3' cols='50' required></textarea><br><br>";
                    }
                    echo "</fieldset>";
                }
                ?>
            </fieldset>

            <button type="submit" class="submit-btn">Submit</button>
        </form>
        <div class="nav-buttons-row">
            <button id="back-btn" class="nav-btn nav-btn-left" onclick="window.history.back();return false;">Back</button>
            <button id="next-btn" class="nav-btn nav-btn-right">Next</button>
        </div>
        <?php endif; ?>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('#next-btn').forEach(function(btn) {
                btn.onclick = function() {
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', 'face_image_responses.php?check_only=1', true);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            try {
                                var res = JSON.parse(xhr.responseText);
                                if (res.has_image) {
                                    window.location.href = 'face_image_responses.php';
                                } else {
                                    window.location.href = 'face_image.php';
                                }
                            } catch (e) {
                                window.location.href = 'face_image.php';
                            }
                        }
                    };
                    xhr.send();
                    return false;
                };
            });
        });
    </script>
</body>
</html>