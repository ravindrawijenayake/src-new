<!-- src/html/index.html -->
<!-- filepath: c:\xampp\htdocs\2020FC\src\php\dashboard.php -->
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
    <main>
        <!-- Your website content goes here -->
        <div class="welcome-container">
            <h1>Welcome, <?php echo htmlspecialchars($userName); ?>!</h1>
            <h2 style="color: white;">You are now logged in.</h2>
        </div>
        <div class="intro-container">        
            <h3>To get started, we need to do a psychometric test. Once you complete the test, we will create your avatar.</h3> 
        </div>
        <div class="setup-container">    
            <div class="profile-setup">
                <a href="frontend_psychometric_test.php">
                    <button onclick="startQuestionnaire()"><h1>Take Test</h1></button>
            </div>
            <div class="futureself-setup">
                <a href="futureself.php">
                <button onclick="startFutureSelf()"><h1>Future Self</h1></button>
            </div>
            <div class="avatar-setup">
                <button onclick="uploadAvatar()"><h1>Make Avatar</h1></button>
            </div>
            <div class="chatbot-setup">
                <button onclick="startChatbot()"><h1>Start Advice</h1></button>
            </div>
            <div class="expenditure-setup">
                <a href="expenditure.php">
                    <button onclick="startExpenditure()"><h1>Track Expenditure</h1></button>
                </a>
            </div>
        </div>
    </main>
    <script src="../js/main.js"></script>
</body>
</html>