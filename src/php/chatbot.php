<!-- src/html/index.html -->
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
            </ul>
        </nav>
    </header>
    <main>
        <!-- Your website content goes here -->
         <!-- src/html/chatbot.html -->
<div class="space-bubble-container">
    <div class="bubble" id="chatBubble">
        <div class="chat-messages">
            <div class="message bot">Hello! I'm your FUTURE SELF! What would you like to know?</div>
        </div>
        <div class="input-area">
            <input type="text" id="userInput" placeholder="Type your message...">
        </div>
        <div class="send-button">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>
</div>
    </main>
    <script src="../js/main.js"></script>
</body>
</html>