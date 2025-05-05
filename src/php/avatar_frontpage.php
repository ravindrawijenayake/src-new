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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Your Face Image</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/avatarstyle.css">
    <script>
        const userEmail = "<?php echo $userEmail; ?>";
    </script>
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
                <li><a href="logout.php">Logout <?php echo htmlspecialchars($userName); ?></a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="image-section">
                <h3>Uploaded Face Image</h3>
                <?php if ($faceImageUrl): ?>
                    <div class="image-preview">
                        <img src="<?php echo htmlspecialchars($faceImageUrl); ?>" alt="Uploaded Face Image" style="max-width: 400px;" />
                    </div>
                <?php else: ?>
                    <p>No face image uploaded.</p>
                <?php endif; ?>
            </div>

            <div class="avatar-section">
                <h3>Test Generated Avatar</h3>
                <div id="avatar-preview">
                    <p>No avatar generated yet.</p>
                </div>
                <button id="generate-avatar-btn">Generate Avatar</button>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('generate-avatar-btn').addEventListener('click', function() {
            const button = this;
            button.disabled = true;
            button.textContent = 'Generating...';

            fetch('../php/aged_avatar_gen.php', {
                method: 'POST',
                body: JSON.stringify({ email: userEmail }),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                console.log('Response:', data);
                if (data.status === 'ok') {
                    document.getElementById('avatar-preview').innerHTML = `
                        <img src="${data.avatar_path}?t=${new Date().getTime()}" alt="Generated Avatar" style="max-width:400px;">
                    `;
                } else {
                    alert('Error: ' + data.message);
                    document.getElementById('avatar-preview').innerHTML = `<p>Error generating avatar. Please try again.</p>`;
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                alert('An unexpected error occurred. Please try again later.');
            })
            .finally(() => {
                button.disabled = false;
                button.textContent = 'Generate Avatar';
            });
        });
    </script>
</body>
</html>
