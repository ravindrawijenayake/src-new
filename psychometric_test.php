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

// Check if user already has a record
$stmt = $pdo->prepare("SELECT responses, dominant_belief, money_avoidance, money_worship, money_status, money_vigilance FROM psychometric_test_responses WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $userEmail]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>20:20 FC - FINEDICA</title>
    <link rel="stylesheet" href="psychometric_test_style.css">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="../css/progressbar.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>20:20 FC - FINEDICA</h1>
                <p>Expert Financial Coaching</p>
            </div>
            <ul>
                <li><a href="..php/index.php">Home</a></li>
                <li><a href="..2020FC/src/php/questionnaire.php">Questionnaire</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="avatar_frontpage.php">Avatar</a></li>
                <li><a href="..src/chatbot/chatbot.php">Chatbot</a></li>
                <li><a href="..src/php/logout.php" style="font-size: 14px; color:rgb(7, 249, 168)">Logout <?php echo htmlspecialchars($userName); ?></a></li>
            </ul>
        </nav>  
    </header>
    <?php $progressStep = 1; include '../php/progressbar.php'; ?>
    <div class="container">
        <h1>Psychometric Money Belief Test</h1>
        <div id="already-submitted" style="display:none">
            <h2>🏆 Your Money Belief is </h2>
            <h3 id="prev-dominant"></h3>
            <h4>Category Scores</h4>
            <ul id="prev-scores"></ul>
            <h4>Your Responses</h4>
            <ul id="prev-answers"></ul>
            <div class="nav-buttons-row">
                <button id="back-btn" class="nav-btn nav-btn-left">Back</button>
                <button id="next-btn" class="nav-btn nav-btn-right">Next</button>
            </div>
        </div>
        <form id="psychometricForm" style="display:none">
            <div id="questionsContainer">
                <p> (1 = Strongly Disagree, 5 = Strongly Agree)</p>
                <!-- Questions will be dynamically loaded here -->
            </div>
            <button type="button" id="review-answers" style="margin-top: 20px;">Review Answers</button>
            <button type="submit" id="submit-btn" style="display:none;">Submit</button>
        </form>
        <div id="review-container" style="display: none; margin-top: 20px; padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9;">
            <h3>Review Your Answers</h3>
            <div id="review-list"></div>
            <button id="edit-answers" style="margin-top:10px;">Edit Answers</button>
            <button id="final-submit" style="margin-top:10px;">Submit</button>
        </div>
        <div id="result" class="hidden">
            <!-- Removed duplicate result labels from here -->
        </div>
    </div>
    <style>
        .nav-buttons-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }
        .nav-btn {
            padding: 12px 32px;
            font-size: 1.1em;
            border: none;
            border-radius: 6px;
            background: linear-gradient(90deg, #21f336 0%, #2196f3 100%);
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(33,150,243,0.08);
            transition: background 0.2s, transform 0.2s;
        }
        .nav-btn-left {
            margin-right: auto;
        }
        .nav-btn-right {
            margin-left: auto;
        }
        .nav-btn:hover {
            background: linear-gradient(90deg, #2196f3 0%, #21f336 100%);
            transform: translateY(-2px) scale(1.04);
        }
    </style>
    <script>
        // Load questions from a static JSON file
        let questionsByCategory = {};
        let flatQuestions = [];
        let existing = <?php echo json_encode($existing ? $existing : null); ?>;
        document.addEventListener('DOMContentLoaded', function () {
            if (existing) {
                // Show previous results
                document.getElementById('already-submitted').style.display = '';
                document.getElementById('psychometricForm').style.display = 'none';
                document.getElementById('result').classList.add('hidden');
                document.getElementById('prev-dominant').textContent = existing.dominant_belief;
                // Scores
                const scoresList = document.getElementById('prev-scores');
                scoresList.innerHTML = '';
                scoresList.innerHTML += `<li>Money Avoidance: ${existing.money_avoidance}</li>`;
                scoresList.innerHTML += `<li>Money Worship: ${existing.money_worship}</li>`;
                scoresList.innerHTML += `<li>Money Status: ${existing.money_status}</li>`;
                scoresList.innerHTML += `<li>Money Vigilance: ${existing.money_vigilance}</li>`;
                // Answers
                fetch('questions.json').then(res=>res.json()).then(data=>{
                    let flatQ = [];
                    for (const qs of Object.values(data)) for (const q of qs) flatQ.push(q);
                    const prevAnswers = JSON.parse(existing.responses);
                    let ansList = document.getElementById('prev-answers');
                    ansList.innerHTML = '';
                    let idx = 0;
                    for (const cat of Object.keys(data)) {
                        for (let i=0; i<data[cat].length; i++) {
                            let val = prevAnswers[cat][i];
                            ansList.innerHTML += `<li>${idx+1}. ${flatQ[idx]}: ${answerMeanings[val]||val||'No answer'}</li>`;
                            idx++;
                        }
                    }
                });
                // Navigation
                document.getElementById('back-btn').onclick = ()=>window.history.back();
                document.getElementById('next-btn').onclick = ()=>window.location.href='../future_self/futureself.php';
                return;
            }
            // If not submitted, show form
            document.getElementById('psychometricForm').style.display = '';
            fetch('questions.json')
            .then(res => res.json())
            .then(data => {
                const questionsContainer = document.getElementById('questionsContainer');
                questionsByCategory = data;
                flatQuestions = [];
                let qNum = 1;
                for (const qs of Object.values(questionsByCategory)) {
                    for (const q of qs) {
                        flatQuestions.push(q);
                        const questionDiv = document.createElement('div');
                        questionDiv.classList.add('question-item');
                        questionDiv.innerHTML = `
                            <label>${qNum}. ${q}</label>
                            <div class="radio-group">
                                <label><input type="radio" name="question_${qNum-1}" value="1" required> 1</label>
                                <label><input type="radio" name="question_${qNum-1}" value="2"> 2</label>
                                <label><input type="radio" name="question_${qNum-1}" value="3"> 3</label>
                                <label><input type="radio" name="question_${qNum-1}" value="4"> 4</label>
                                <label><input type="radio" name="question_${qNum-1}" value="5"> 5</label>
                            </div>
                        `;
                        questionsContainer.appendChild(questionDiv);
                        qNum++;
                    }
                }
            })
            .catch(err => {
                console.error('Error fetching questions:', err);
                alert('Failed to load questions. Please try again later.');
            });
        });

        // Map number to meaning
        const answerMeanings = {
            1: 'Strongly Disagree',
            2: 'Disagree',
            3: 'Neutral',
            4: 'Agree',
            5: 'Strongly Agree'
        };

        // Review answers before submission
        document.getElementById("review-answers").addEventListener("click", function () {
            const reviewContainer = document.getElementById("review-container");
            const reviewList = document.getElementById("review-list");
            reviewList.innerHTML = "";
            let allAnswered = true;
            flatQuestions.forEach((q, i) => {
                const val = document.querySelector(`input[name='question_${i}']:checked`);
                const reviewItem = document.createElement("p");
                if (val) {
                    reviewItem.textContent = `${i+1}. ${q}: ${answerMeanings[val.value]}`;
                } else {
                    reviewItem.textContent = `${i+1}. ${q}: No answer selected`;
                    allAnswered = false;
                }
                reviewList.appendChild(reviewItem);
            });
            if (!allAnswered) {
                alert('Please answer all questions before reviewing.');
                return;
            }
            reviewContainer.style.display = "block";
        });
        document.getElementById("edit-answers").addEventListener("click", function () {
            document.getElementById("review-container").style.display = "none";
        });
        document.getElementById("final-submit").addEventListener("click", function () {
            // Collect answers in the format: { category: [1,2,3,4,5], ... }
            const responses = {};
            let allAnswered = true;
            let qIndex = 0;
            for (const [category, qs] of Object.entries(questionsByCategory)) {
                responses[category] = [];
                for (let i = 0; i < qs.length; i++) {
                    const val = document.querySelector(`input[name='question_${qIndex}']:checked`);
                    if (!val) allAnswered = false;
                    responses[category][i] = val ? parseInt(val.value) : null;
                    qIndex++;
                }
            }
            if (!allAnswered) {
                alert('Please answer all questions before submitting.');
                return;
            }
            fetch('save_psychometric_results.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ responses })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('result').classList.remove('hidden');
                    document.getElementById('psychometricForm').style.display = 'none';
                    document.getElementById('review-container').style.display = 'none';
                    document.getElementById('dominant-belief').textContent = data.dominant_belief;
                    const scoresList = document.getElementById('category-scores');
                    scoresList.innerHTML = '';
                    for (const [cat, score] of Object.entries(data.scores)) {
                        const li = document.createElement('li');
                        li.textContent = `${cat}: ${score}`;
                        scoresList.appendChild(li);
                    }
                    document.getElementById('back-btn2').onclick = ()=>window.history.back();
                    document.getElementById('next-btn2').onclick = ()=>window.location.href='../future_self/futureself.php';
                } else {
                    alert('Failed to save responses: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error('Error saving responses:', err);
                alert('An error occurred while saving your responses. Please try again.');
            });
        });
    </script>
</body>
</html>
