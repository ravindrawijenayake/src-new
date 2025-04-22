<!-- filepath: c:\xampp\htdocs\2020FC\src\php\psychometric_test.php -->
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userName = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>20:20 FC - FINEDICA</title>
    <link rel="stylesheet" href="../css/main.css">
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
                <li><a href="logout.php" style="font-size: 14px; color:rgb(7, 249, 168)">Logout <?php echo htmlspecialchars($userName);?></a></li>
            </ul>
        </nav>  
    </header>


<body>
    <header>
        <h1>Psychometric Test</h1>
        <p>Rate each statement from 1 to 5:<br>1 = Strongly Disagree, 5 = Strongly Agree</p>
    </header>
    <main>
        <form action="process_psychometric_test.php" method="POST">
            <?php
            // Define questions for each category
            $questions = [
                "Money Avoidance" => [
                    "I do not deserve a lot of money when others have less than me.",
                    "Rich people are greedy.",
                    "People get rich by taking advantage of others.",
                    "I do not deserve money.",
                    "Good people should not care about money."
                ],
                "Money Worship" => [
                    "Things would get better if I had more money.",
                    "More money will make you happier.",
                    "It is hard to be poor and happy.",
                    "You can never have enough money.",
                    "Money is power."
                ],
                "Money Status" => [
                    "Most poor people do not deserve to have money.",
                    "You can have love or money, but not both.",
                    "I will not buy something unless it is new (e.g., car, house).",
                    "Poor people are lazy.",
                    "Money is what gives life meaning."
                ],
                "Money Vigilance" => [
                    "You should not tell others how much money you have or make.",
                    "It is wrong to ask others how much money they have or make.",
                    "Money should be saved not spent.",
                    "It is important to save for a rainy day.",
                    "People should work for their money and not be given financial handouts."
                ]
            ];

            // Generate the form dynamically
            foreach ($questions as $category => $qs) {
                echo "<fieldset>";
                echo "<legend>$category</legend>";
                foreach ($qs as $index => $question) {
                    echo "<label>$question</label><br>";
                    for ($i = 1; $i <= 5; $i++) {
                        echo "<input type='radio' name='{$category}[$index]' value='$i' required> $i ";
                    }
                    echo "<br><br>";
                }
                echo "</fieldset>";
            }
            ?>
            <button type="submit">Submit</button>
        </form>
    </main>
</body>
</html>