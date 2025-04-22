<!-- filepath: c:\xampp\htdocs\2020FC\src\php\future_self.php -->
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

    // Test query
    $stmt = $pdo->query("SELECT 1");
    echo "Database connection successful.<br>";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

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
                echo "<h3>Error inserting data: " . implode(", ", $stmt->errorInfo()) . "</h3>";
                exit;
            }
        }
    }

    echo "<h3>Thank you! Your responses have been saved.</h3>";
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
        <h1>Future Self</h1>
        <p>Select a stage of life and answer the questions to imagine your future self.</p>
        <form action="futureself.php" method="POST">
            <fieldset>
                <legend>Select a Stage of Life</legend>
                <label><input type="radio" name="stage" value="Buying your first home" required> Age 18-30: Buying your first home</label><br>
                <label><input type="radio" name="stage" value="Becoming a Parent" required> Age 25-35: Becoming a Parent</label><br>
                <label><input type="radio" name="stage" value="Planning Retirement" required> Age 35-50: Planning Retirement</label><br>
                <label><input type="radio" name="stage" value="Retirement" required> Age 60+: Retirement</label><br>
                <label><input type="radio" name="stage" value="General Financial Coaching" required> General Financial Coaching</label>
            </fieldset>

            <fieldset>
                <legend>Questions</legend>
                <?php
                // Define questions for each category
                $categories = [
                    "Physicality" => [
                        "How do you imagine your physical health at this stage?",
                        "What activities will you do to stay healthy?"
                    ],
                    "Finances" => [
                        "What will your financial situation look like?",
                        "What steps will you take to achieve financial stability?"
                    ],
                    "Emotional/spiritual" => [
                        "How will you maintain emotional and spiritual well-being?",
                        "What practices will you follow to stay emotionally balanced?"
                    ],
                    "LifeStyle" => [
                        "What will your daily lifestyle look like?",
                        "What hobbies or interests will you pursue?"
                    ],
                    "Profession" => [
                        "What will your professional life look like?",
                        "What goals will you set for your career?"
                    ],
                    "Relationships" => [
                        "What will your relationships with family and friends look like?",
                        "How will you nurture these relationships?"
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

            <button type="submit">Submit</button>
        </form>
    </main>
</body>
</html>