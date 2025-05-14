<?php
session_start();

// Check login
if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

$userEmail = $_SESSION['user_email'];
$userName = $_SESSION['user_name'];

// Database connection
$host = 'localhost';
$dbname = 'user_reg_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT face_image_url FROM face_image_responses WHERE email = :email ORDER BY id DESC LIMIT 1");
    $stmt->bindParam(':email', $userEmail);
    $stmt->execute();
    $faceImageUrl = $stmt->fetchColumn();
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if avatar exists for this user and get its path
$avatarPath = null;
try {
    $stmt = $pdo->prepare("SELECT image_path FROM avatars WHERE email = :email LIMIT 1");
    $stmt->bindParam(':email', $userEmail);
    $stmt->execute();
    $avatarPath = $stmt->fetchColumn();
} catch (PDOException $e) {
    $avatarPath = null;
}

// Only show generate button if no avatar exists, otherwise show re-generate button
$showGenerate = !$avatarPath;
$showRegenerate = (bool)$avatarPath;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Your Face Image</title>
    <link rel="stylesheet" href="../css/main.css">

    <link rel="stylesheet" href="../css/progressbar.css">
    <link rel="stylesheet" href="face_image_style.css">
    <script>
        const userEmail = "<?php echo $_SESSION['user_email']; ?>";
    </script>
    <script>
        window.userEmail = "<?php echo $_SESSION['user_email']; ?>";
    </script>
    <script src="../js/avatar.js"></script>
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
                <li><a href="../php/logout.php">Logout <?php echo htmlspecialchars($userName); ?></a></li>
            </ul>
        </nav>
    </header>
    <?php $progressStep = 3; include '../php/progressbar.php'; ?>
    <main>

        <div class="face-image-avatar-container">
            <div class="face-image-section">
                <h3>Uploaded Face Image</h3>
                <div class="image-preview">
                    <?php if ($faceImageUrl): ?>
                        <img src="<?php echo htmlspecialchars($faceImageUrl); ?>" alt="Uploaded Face Image" />
                    <?php else: ?>
                        <p>No face image uploaded.</p>
                    <?php endif; ?>
                </div>
                <?php if ($faceImageUrl): ?>
                    <button id="reupload-face-btn">Re-upload Face Image</button>
                <?php endif; ?>
            </div>
            <div class="avatar-section">
                <h3>Generated Avatar</h3>
                <div id="avatar-preview">
                    <?php if ($avatarPath): ?>
                        <img src="/2020FC/src/avatars/<?php echo htmlspecialchars($avatarPath); ?>?t=<?php echo time(); ?>" alt="Generated Avatar">
                    <?php else: ?>
                        <p>No avatar generated yet.</p>
                    <?php endif; ?>
                </div>
                <?php if ($showGenerate): ?>
                    <button id="generate-avatar-btn">Generate Avatar</button>
                <?php endif; ?>
                <?php if ($showRegenerate): ?>
                    <button id="regenerate-avatar-btn">Re-generate Avatar</button>
                <?php endif; ?>
                <div id="navigation-buttons">
                    <button id="back-btn" type="button">Back</button>
                    <button id="next-btn" type="button">Next</button>
                </div>
            </div>
        </div>
        <?php if (isset($_SESSION['submitted_stage']) && isset($_SESSION['submitted_responses'])): ?>
        <div class="review-container card" style="margin-top:40px;">
            <h3><span class="icon">📝</span> Review Your Responses</h3>
            <div class="stage-selected"><strong>Stage of Life:</strong> <?php echo htmlspecialchars($_SESSION['submitted_stage']); ?></div>
            <?php foreach ($_SESSION['submitted_responses'] as $category => $questions): ?>
                <div class="category-container">
                    <h4 class="category-title"><?php echo htmlspecialchars($category); ?></h4>
                    <ul class="qa-list">
                        <?php foreach ($questions as $question => $response): ?>
                            <li>
                                <span class="question-text"><?php echo htmlspecialchars($question); ?></span>
                                <span class="answer-text"><?php echo htmlspecialchars($response); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
    <link rel="stylesheet" href="../css/faceimagestyle.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userEmail = window.userEmail;
            // Navigation buttons
            document.getElementById('back-btn').onclick = function() {
                window.location.href = 'futureself_responses.php';
            };
            document.getElementById('next-btn').onclick = function() {
                window.location.href = '../chatbot/chatbot.php';
            };
            // Generate Avatar button logic
            const genBtn = document.getElementById('generate-avatar-btn');
            if (genBtn) {
                genBtn.onclick = function () {
                    genBtn.disabled = true;
                    genBtn.textContent = 'Generating...';
                    fetch('../php/generate_avatar.php', {
                        method: 'POST',
                        body: JSON.stringify({ email: userEmail }),
                        headers: { 'Content-Type': 'application/json' },
                    })
                        .then((res) => res.json())
                        .then((data) => {
                            if (data.status === 'ok') {
                                document.getElementById('avatar-preview').innerHTML = `<img src="${data.avatar_path}?t=${Date.now()}" alt="Generated Avatar" style="max-width:320px; max-height:320px; border-radius:16px; box-shadow:0 2px 16px rgba(33,150,243,0.16);">`;
                                genBtn.style.display = 'none';
                                // Show re-generate button after generation
                                let regenBtn = document.getElementById('regenerate-avatar-btn');
                                if (!regenBtn) {
                                    regenBtn = document.createElement('button');
                                    regenBtn.id = 'regenerate-avatar-btn';
                                    regenBtn.textContent = 'Re-generate Avatar';
                                    regenBtn.className = 'action-btn';
                                    document.querySelector('.avatar-section').insertBefore(regenBtn, document.getElementById('navigation-buttons'));
                                }
                                regenBtn.onclick = function() {
                                    if (confirm('To re-generate your avatar, you must re-take the Future Self test. Do you want to proceed?')) {
                                        if (confirm('This will delete your previous Future Self responses and avatar. Do you consent to proceed?')) {
                                            fetch('regenerate_avatar_cleanup.php', {
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/json' },
                                                body: JSON.stringify({ email: userEmail })
                                            })
                                            .then(res => res.json())
                                            .then(data => {
                                                if (data.status === 'ok') {
                                                    window.location.href = 'futureself.php';
                                                } else {
                                                    alert('Error: ' + data.message);
                                                }
                                            })
                                            .catch(() => {
                                                alert('An unexpected error occurred. Please try again.');
                                            });
                                        }
                                    }
                                };
                                regenBtn.style.display = 'inline-block';
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(() => {
                            alert('An unexpected error occurred. Please try again later.');
                        })
                        .finally(() => {
                            genBtn.disabled = false;
                            genBtn.textContent = 'Generate Avatar';
                        });
                };
            }
            // Re-generate Avatar button logic
            const regenBtn = document.getElementById('regenerate-avatar-btn');
            if (regenBtn) {
                regenBtn.onclick = function() {
                    if (confirm('To re-generate your avatar, you must re-take the Future Self test. Do you want to proceed?')) {
                        if (confirm('This will delete your previous Future Self responses and avatar. Do you consent to proceed?')) {
                            fetch('regenerate_avatar_cleanup.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ email: userEmail })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.status === 'ok') {
                                    window.location.href = 'futureself.php';
                                } else {
                                    alert('Error: ' + data.message);
                                }
                            })
                            .catch(() => {
                                alert('An unexpected error occurred. Please try again.');
                            });
                        }
                    }
                };
            }
            // Re-upload Face Image button logic
            const reuploadBtn = document.getElementById('reupload-face-btn');
            if (reuploadBtn) {
                reuploadBtn.onclick = function() {
                    if (confirm('Are you sure you want to re-upload your face image?')) {
                        window.location.href = 'face_image.php?reupload=1';
                    }
                };
            }
        });
    </script>
</body>
</html>
