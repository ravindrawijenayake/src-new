<!-- src/html/index.html -->
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
    <title>20:20 FC- FINEDICA</title>
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
                <li><a href="index.php">Home</a></li>
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
         <!-- src/html/chatbot.html -->
         <section class="hero">
            <div class="hero-content">
            <div class="avatar-preview">
                <h2>Your Generated Avatar</h2>
                <div id="avatarContainer">
                    <!-- Add an image placeholder for the avatar -->
                    <img id="avatarImage" src="../images/default-avatar.png" alt="Your Avatar" style="width: 150px; height: 150px; border-radius: 50%; border: 2px solid #2196f3;">
                </div>
            </div>
            </div>

            <div class="space-bubble-container">
                <div class="bubble" id="chatBubble">
                    <div class="chat-messages">
                        <div class="message bot">
                            <h1>Hi <?php echo htmlspecialchars($userName); ?>,</h1>
                            <h3>It's me! Let's talk. You can ask me anything you like</h3>
                                <div class="chat-history" id="chatHistory">
                                    
                    </div>
                    <div class="input-area">
                        <input type="text" id="userInput" placeholder="Type your message...">
                    </div>
                    <div class="send-button">
                        <button onclick="sendMessage()">Send</button>
                    </div>
                </div>
            </div>


                <!-- Chat history will be displayed here -->
            
        </section>
        
    </main>
    <script src="../js/main.js"></script>
</body>
</html>