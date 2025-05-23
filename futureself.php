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
    $pdo = new PDO("mysql:host=$host;port=3307;dbname=$dbname", $username, $password);
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
                <li><a href="../generate_avatar/avatar_frontpage.php">Avatar</a></li>
                <li><a href="../chatbot/chatbot.php">Chatbot</a></li>
                <li><a href="../php/logout.php" style="font-size: 14px; color:rgb(7, 249, 168)">Logout <?php echo htmlspecialchars($userName); ?></a></li>
            </ul>
        </nav>
    </header>
    <?php $progressStep = 2; include '../php/progressbar.php'; ?>
    <main>
        <div class="futureself-hero">
            <h1><span class="icon">🌱</h1>
            <p class="subtitle">Select a stage of life and answer the questions to envision your future.</p>  
        </div>

        <div class="futureself-intro enhanced-intro-card">
            <div class="intro-icon">🌟</div>
            <div class="intro-content">
                <h2>Envision Your Future Self</h2>
                <p>These questions help FINEDICA construct an avatar of your future self when you have achieved your most relevant financial goal.</p>
                <ul>
                    <li>If you are trying to buy your first home, your future self will be the age at which you wish to get on the property ladder.</li>
                    <li>If you are planning retirement, then your future self will be at your desired retirement age.</li>
                </ul>
                <p>Before answering, think carefully about your responses. The more detail you put into nailing your future self, the more your brain will buy into it. Be realistic: for example, if you are buying your first home at 30, you may not be earning £100,000 a year, but you may have a lot of money in a savings vehicle like a lifetime ISA.</p>
            </div>
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
                    <button id="next-category" class="nav-btn nav-btn-right" onclick="window.location.href='face_image.php';">Next</button>
                </div>
            </div>
        <?php else: ?>
        <form action="futureself.php" method="POST" class="futureself-form card" id="futureself-form" autocomplete="off">
            <fieldset id="category-0" class="category-section" style="display:block;">
                <legend><span class="icon">🎯</span> Select a Stage of Life</legend>
                <label><input type="radio" name="stage" value="Buying your first home" required> Age 18-30: Buying your first home</label><br>
                <label><input type="radio" name="stage" value="Becoming a Parent" required> Age 25-35: Becoming a Parent</label><br>
                <label><input type="radio" name="stage" value="Planning Retirement" required> Age 35-50: Planning Retirement</label><br>
                <label><input type="radio" name="stage" value="Retirement" required> Age 60+: Retirement</label><br>
                <label><input type="radio" name="stage" value="General Financial Coaching" required> General Financial Coaching</label>
            </fieldset>
            <fieldset id="category-1" class="category-section" style="display:none;">
                <legend>Physicality</legend>
                <label>1. How old will you be when you achieve your financial goal?</label><br>
                <select name="responses[Physicality][1. How old will you be when you achieve your financial goal?]" required>
                    <option value="">Select...</option>
                    <option>25 to 30</option>
                    <option>31 to 35</option>
                    <option>36 to 40</option>
                    <option>41 to 45</option>
                    <option>46 to 50</option>
                    <option>51 to 55</option>
                    <option>56 to 60</option>
                    <option>61 to 65</option>
                    <option>66 to 70</option>
                    <option>70 plus</option>
                </select><br><br>
                <label>2. What colour will your hair be?</label><br>
                <select name="responses[Physicality][2. What colour will your hair be?]" class="hair-colour-select" required>
                    <option value="">Select...</option>
                    <option>Black/Brown</option>
                    <option>Red</option>
                    <option>Blonde</option>
                    <option>Grey</option>
                    <option value="Other">Other (please specify)</option>
                </select>
                <input type="text" name="responses[Physicality][2. What colour will your hair be? - Other]" class="hair-colour-other" style="display:none;" placeholder="Please specify" /><br><br>
                <label>3. How will your posture exude confidence and satisfaction in achieving a major financial milestone?</label><br>
                <select name="responses[Physicality][3. How will your posture exude confidence and satisfaction in achieving a major financial milestone?]" class="posture-select" required>
                    <option value="">Select...</option>
                    <option>More relaxed</option>
                    <option>More confident</option>
                    <option>Act with more purpose</option>
                    <option>More jovial</option>
                    <option>No change</option>
                    <option value="Other">Other (please specify)</option>
                </select>
                <input type="text" name="responses[Physicality][3. How will your posture exude confidence and satisfaction in achieving a major financial milestone? - Other]" class="posture-other" style="display:none;" placeholder="Please specify" /><br><br>
                <label>4. How will you dress?</label><br>
                <select name="responses[Physicality][4. How will you dress?]" class="dress-select" required>
                    <option value="">Select...</option>
                    <option>Business attire</option>
                    <option>Smart casual</option>
                    <option>Casual</option>
                    <option>Sportswear</option>
                    <option value="Other">Other (please specify)</option>
                </select>
                <input type="text" name="responses[Physicality][4. How will you dress? - Other]" class="dress-other" style="display:none;" placeholder="Please specify" /><br><br>
                <label>5. What standard of clothes will you buy?</label><br>
                <select name="responses[Physicality][5. What standard of clothes will you buy?]" required>
                    <option value="">Select...</option>
                    <option>Designer labels</option>
                    <option>High street labels</option>
                    <option>Non-branded clothes</option>
                </select><br><br>
                <label>6. Do you exercise?</label><br>
                <select name="responses[Physicality][6. Do you exercise?]" required>
                    <option value="">Select...</option>
                    <option>Gym</option>
                    <option>Workout at home</option>
                    <option>I don’t exercise</option>
                </select><br><br>
            </fieldset>
            <fieldset id="category-2" class="category-section" style="display:none;">
                <legend>Income and Personal Finances</legend>
                <label>1. How much money will you earn?</label><br>
                <select name="responses[Income and Personal Finances][1. How much money will you earn?]" class="income-earn-select" required>
                    <option value="">Select...</option>
                    <option>£15,0000 to 25,000</option>
                    <option>£25,001 to £35,000</option>
                    <option>£35,001 to £40,000</option>
                    <option>£40,001 to £45,000</option>
                    <option>£45,0001 to £50,000</option>
                    <option>£50,000 plus</option>
                    <option value="Other">Other (please specify)</option>
                </select>
                <input type="text" name="responses[Income and Personal Finances][1. How much money will you earn? - Other]" class="income-earn-other" style="display:none;" placeholder="Please specify" /><br><br>
                <label>2. What is your most valuable financial asset (house, etc)?</label><br>
                <select name="responses[Income and Personal Finances][2. What is your most valuable financial asset (house, etc)?]" class="asset-select" required>
                    <option value="">Select...</option>
                    <option>House</option>
                    <option>Investments (e.g ISAs)</option>
                    <option>Pension</option>
                    <option value="Other">Other (please specify)</option>
                </select>
                <input type="text" name="responses[Income and Personal Finances][2. What is your most valuable financial asset (house, etc)? - Other]" class="asset-other" style="display:none;" placeholder="Please specify" /><br><br>
                <label>3. Will you have a mortgage?</label><br>
                <select name="responses[Income and Personal Finances][3. Will you have a mortgage?]" required>
                    <option value="">Select...</option>
                    <option>Yes</option>
                    <option>No</option>
                    <option>Don’t know</option>
                </select><br><br>
                <label>4. What investments will you have?</label><br>
                <select name="responses[Income and Personal Finances][4. What investments will you have?]" required>
                    <option value="">Select...</option>
                    <option>Individual Savings Accounts (ISAs)</option>
                    <option>Cash savings plans</option>
                    <option>No savings</option>
                </select><br><br>
                <label>5. How will you have saved?</label><br>
                <select name="responses[Income and Personal Finances][5. How will you have saved?]" class="saved-select" required>
                    <option value="">Select...</option>
                    <option>£0 to £5,000</option>
                    <option>£5,001 to £10,000</option>
                    <option>£10,001 to £15,000</option>
                    <option>£15,001 to £20,000</option>
                    <option value="Other">Other (please specify)</option>
                </select>
                <input type="text" name="responses[Income and Personal Finances][5. How will you have saved? - Other]" class="saved-other" style="display:none;" placeholder="Please specify" /><br><br>
                <label>6. How much disposable income will you have at this time?</label><br>
                <select name="responses[Income and Personal Finances][6. How much disposable income will you have at this time?]" required>
                    <option value="">Select...</option>
                    <option>Up to 10% of your income</option>
                    <option>Up to 20% of your income</option>
                    <option>Up to 30% of your income</option>
                    <option>30% above</option>
                </select><br><br>
            </fieldset>
            <fieldset id="category-3" class="category-section" style="display:none;">
                <legend>Emotional/Spiritual Values</legend>
                <label>1. What will achieving this goal do for you as a person? For instance, if you sacrifice to get on the property ladder, will you have a sense of pride and achievement?</label><br>
                <select name="responses[Emotional/Spiritual Values][1. What will achieving this goal do for you as a person? For instance, if you sacrifice to get on the property ladder, will you have a sense of pride and achievement?]" required>
                    <option value="">Select...</option>
                    <option>Feel more secure</option>
                    <option>Feel accomplished</option>
                    <option>Negative feelings, perhaps you don’t like the sacrifice?</option>
                    <option>Feel responsible</option>
                </select><br><br>
                <label>2. What are your core values (pick three)?</label><br>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][2. What are your core values (pick three)?][]" value="Love"> Love</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][2. What are your core values (pick three)?][]" value="Charity"> Charity</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][2. What are your core values (pick three)?][]" value="Gratitude"> Gratitude</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][2. What are your core values (pick three)?][]" value="Resilience"> Resilience</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][2. What are your core values (pick three)?][]" value="Honesty"> Honesty</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][2. What are your core values (pick three)?][]" value="Ambition"> Ambition</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][2. What are your core values (pick three)?][]" value="Honour"> Honour</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][2. What are your core values (pick three)?][]" value="Sacrifice"> Sacrifice</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][2. What are your core values (pick three)?][]" value="Community"> Community</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][2. What are your core values (pick three)?][]" value="Spirituality"> Spirituality</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][2. What are your core values (pick three)?][]" value="Faith"> Faith</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][2. What are your core values (pick three)?][]" value="Kindness"> Kindness</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][2. What are your core values (pick three)?][]" value="Other" class="core-values-other-checkbox"> Other (please specify)</label>
                    <input type="text" name="responses[Emotional/Spiritual Values][2. What are your core values (pick three)? - Other]" class="core-values-other" style="display:none;" placeholder="Please specify" />
                </div><br>
                <label>3. How will you keep yourself emotionally balanced and healthy (pick three)?</label><br>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][3. How will you keep yourself emotionally balanced and healthy (pick three)?][]" value="Faith"> Faith</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][3. How will you keep yourself emotionally balanced and healthy (pick three)?][]" value="Meditation"> Meditation</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][3. How will you keep yourself emotionally balanced and healthy (pick three)?][]" value="Exercise"> Exercise</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][3. How will you keep yourself emotionally balanced and healthy (pick three)?][]" value="Support from friends and family"> Support from friends and family</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][3. How will you keep yourself emotionally balanced and healthy (pick three)?][]" value="Other" class="balanced-other-checkbox"> Other (please specify)</label>
                    <input type="text" name="responses[Emotional/Spiritual Values][3. How will you keep yourself emotionally balanced and healthy (pick three)? - Other]" class="balanced-other" style="display:none;" placeholder="Please specify" />
                </div><br>
                <label>4. How do your core values relate to your financial goal (pick two)?</label><br>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][4. How do your core values relate to your financial goal (pick two)?][]" value="Help you stay grounded"> Help you stay grounded</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][4. How do your core values relate to your financial goal (pick two)?][]" value="Help you stay resilient"> Help you stay resilient</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][4. How do your core values relate to your financial goal (pick two)?][]" value="Help you dream big for the future"> Help you dream big for the future</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][4. How do your core values relate to your financial goal (pick two)?][]" value="Help you stay on track"> Help you stay on track</label>
                    <label><input type="checkbox" name="responses[Emotional/Spiritual Values][4. How do your core values relate to your financial goal (pick two)?][]" value="Other" class="core-values-relate-other-checkbox"> Other (please specify)</label>
                    <input type="text" name="responses[Emotional/Spiritual Values][4. How do your core values relate to your financial goal (pick two)? - Other]" class="core-values-relate-other" style="display:none;" placeholder="Please specify" />
                </div><br>
            </fieldset>
            <fieldset id="category-4" class="category-section" style="display:none;">
                <legend>LifeStyle</legend>
                <label>1. What will your hobbies and interests be?</label><br>
                <select name="responses[LifeStyle][1. What will your hobbies and interests be?]" class="hobbies-select" required>
                    <option value="">Select...</option>
                    <option>Sport</option>
                    <option>Exercise</option>
                    <option>Reading</option>
                    <option>Gaming</option>
                    <option value="Other">Other (please specify)</option>
                </select>
                <input type="text" name="responses[LifeStyle][1. What will your hobbies and interests be? - Other]" class="hobbies-other" style="display:none;" placeholder="Please specify" /><br><br>
                <label>2. How many holidays a year will you go on?</label><br>
                <select name="responses[LifeStyle][2. How many holidays a year will you go on?]" required>
                    <option value="">Select...</option>
                    <option>One</option>
                    <option>Two</option>
                    <option>Three</option>
                    <option>Three plus</option>
                </select><br><br>
                <label>3. What will you have to sacrifice to achieve your financial goals?</label><br>
                <select name="responses[LifeStyle][3. What will you have to sacrifice to achieve your financial goals?]" class="sacrifice-select" required>
                    <option value="">Select...</option>
                    <option>Make sacrifices in your leisure spending</option>
                    <option>Holidays</option>
                    <option>Alcohol or cigarettes</option>
                    <option>Leisure time (i.e. through working extra hours)</option>
                    <option value="Other">Other (please specify)</option>
                </select>
                <input type="text" name="responses[LifeStyle][3. What will you have to sacrifice to achieve your financial goals? - Other]" class="sacrifice-other" style="display:none;" placeholder="Please specify" /><br><br>
                <label>4. How can you reframe these sacrifices?</label><br>
                <select name="responses[LifeStyle][4. How can you reframe these sacrifices?]" class="reframe-select" required>
                    <option value="">Select...</option>
                    <option>I am investing in my future self</option>
                    <option>I am achieving something meaningful</option>
                    <option>I am building financial security</option>
                    <option>I am building financial security for my family</option>
                    <option value="Other">Other (please specify)</option>
                </select>
                <input type="text" name="responses[LifeStyle][4. How can you reframe these sacrifices? - Other]" class="reframe-other" style="display:none;" placeholder="Please specify" /><br><br>
                <label>5. How do you balance your lifestyle against achieving and maintaining your financial goals?</label><br>
                <select name="responses[LifeStyle][5. How do you balance your lifestyle against achieving and maintaining your financial goals?]" required>
                    <option value="">Select...</option>
                    <option>Budgeting</option>
                    <option>Journaling</option>
                    <option>Using an app</option>
                    <option>Automating a set amount to save each month</option>
                    <option>Accountability partner</option>
                </select><br><br>
            </fieldset>
            <fieldset id="category-5" class="category-section" style="display:none;">
                <legend>Profession</legend>
                <label>1. What do you do for work?</label><br>
                <input type="text" name="responses[Profession][1. What do you do for work?]" required placeholder="Your answer"><br><br>
                <label>2. How much do you earn?</label><br>
                <select name="responses[Profession][2. How much do you earn?]" required>
                    <option value="">Select...</option>
                    <option>£15,000 to £25,000</option>
                    <option>£25,001 to 35,000</option>
                    <option>£35,001 to £45,000</option>
                    <option>£45,001 to £55,000</option>
                    <option>£55,000 above</option>
                </select><br><br>
                <label>3. Have you gained a promotion to achieve your financial goals?</label><br>
                <select name="responses[Profession][3. Have you gained a promotion to achieve your financial goals?]" required>
                    <option value="">Select...</option>
                    <option>Yes</option>
                    <option>No</option>
                </select><br><br>
                <label>4. What has your career trajectory been?</label><br>
                <select name="responses[Profession][4. What has your career trajectory been?]" required>
                    <option value="">Select...</option>
                    <option>Steady</option>
                    <option>Not changed</option>
                    <option>Drastically improved</option>
                </select><br><br>
                <label>5. How did you motivate yourself to stay the course to achieve your financial goals?</label><br>
                <select name="responses[Profession][5. How did you motivate yourself to stay the course to achieve your financial goals?]" required>
                    <option value="">Select...</option>
                    <option>Visualisation</option>
                    <option>Financial accountability partner</option>
                    <option>Savings apps</option>
                    <option>Budgeting apps</option>
                    <option>Financial coaching/advice</option>
                </select><br><br>
                <label>6. How did you maintain a good work-life balance?</label><br>
                <input type="text" name="responses[Profession][6. How did you maintain a good work-life balance?]" required placeholder="Your answer"><br><br>
            </fieldset>
            <fieldset id="category-6" class="category-section" style="display:none;">
                <legend>Relationships</legend>
                <label>1. Do you have family?</label><br>
                <select name="responses[Relationships][1. Do you have family?]" required>
                    <option value="">Select...</option>
                    <option>Yes</option>
                    <option>No</option>
                    <option>Maybe</option>
                </select><br><br>
                <label>2. Who are your friends?</label><br>
                <select name="responses[Relationships][2. Who are your friends?]" class="friends-select" required>
                    <option value="">Select...</option>
                    <option>Work friends</option>
                    <option>Same core friendship group</option>
                    <option value="Other">Other (please specify)</option>
                </select>
                <input type="text" name="responses[Relationships][2. Who are your friends? - Other]" class="friends-other" style="display:none;" placeholder="Please specify" /><br><br>
                <label>3. What do you do with your friends?</label><br>
                <select name="responses[Relationships][3. What do you do with your friends?]" class="friends-do-select" required>
                    <option value="">Select...</option>
                    <option>Sports</option>
                    <option>Socialising</option>
                    <option>Practice your faith</option>
                    <option value="Other">Other (please specify)</option>
                </select>
                <input type="text" name="responses[Relationships][3. What do you do with your friends? - Other]" class="friends-do-other" style="display:none;" placeholder="Please specify" /><br><br>
                <label>4. Do your relationships support you in achieving your financial goals?</label><br>
                <select name="responses[Relationships][4. Do your relationships support you in achieving your financial goals?]" required>
                    <option value="">Select...</option>
                    <option>Yes</option>
                    <option>No</option>
                    <option>Not sure</option>
                </select><br><br>
                <label>5. What financial boundaries will you need to set and maintain to achieve your financial goals?</label><br>
                <select name="responses[Relationships][5. What financial boundaries will you need to set and maintain to achieve your financial goals?]" class="boundaries-select" required>
                    <option value="">Select...</option>
                    <option>Limiting time people who encourage you to spend too much money, or damage your financial well-being</option>
                    <option>Letting go of relationships which stop you becoming your future self</option>
                    <option>Practice having tough conversations</option>
                    <option value="Other">Other (please specify)</option>
                </select>
                <input type="text" name="responses[Relationships][5. What financial boundaries will you need to set and maintain to achieve your financial goals? - Other]" class="boundaries-other" style="display:none;" placeholder="Please specify" /><br><br>
                <label>6. What type of people do you associate with?</label><br>
                <input type="text" name="responses[Relationships][6. What type of people do you associate with?]" required placeholder="Your answer"><br><br>
            </fieldset>
            <fieldset id="review-section" class="category-section" style="display:none;">
                <legend>Review Your Answers</legend>
                <div id="review-content"></div>
                <div class="nav-buttons-row">
                    <button type="button" id="edit-answers" class="nav-btn">Edit Answers</button>
                    <button type="submit" class="submit-btn" disabled>Confirm & Submit</button>
                </div>
            </fieldset>
            <div class="nav-buttons-row">
                <button type="button" id="prev-category" class="nav-btn nav-btn-left" style="display:none;">Back</button>
                <button type="button" id="next-category" class="nav-btn nav-btn-right">Next</button>
                <button type="button" id="review-btn" class="nav-btn nav-btn-right" style="display:none;">Review Answers</button>
            </div>
        </form>
        <div id="success-message" class="banner success-banner" style="display:none; margin-top:20px;">Thank you! Your responses have been saved successfully.</div>
        <script>
        // --- State for answers ---
        let answers = {};
        // --- Save all answers in a flat structure for each section ---
        function saveCurrentSection(idx) {
            const fs = document.getElementById('category-' + idx);
            if (!fs) return;
            answers[idx] = {};
            // Save all select fields
            fs.querySelectorAll('select').forEach(function(select) {
                let label = select.closest('label') ? select.closest('label').innerText.trim() : getPreviousLabelText(select);
                if (label) {
                    answers[idx][label] = select.value;
                    // If 'Other' is selected, save the corresponding text
                    if (select.value === 'Other') {
                        const otherInput = select.parentElement.querySelector('input[type="text"]');
                        if (otherInput && otherInput.value) {
                            answers[idx][label + ' - Other'] = otherInput.value;
                        }
                    }
                }
            });
            // Save all text inputs (for 'Other' and free text)
            fs.querySelectorAll('input[type="text"]').forEach(function(input) {
                let label = getPreviousLabelText(input);
                if (label && input.value) {
                    answers[idx][label] = input.value;
                }
            });
            // Save all checkbox groups
            fs.querySelectorAll('.checkbox-group').forEach(function(group) {
                const label = group.previousElementSibling ? group.previousElementSibling.innerText.trim() : '';
                if (label) {
                    const checked = Array.from(group.querySelectorAll('input[type="checkbox"]')).filter(cb => cb.checked).map(cb => cb.value);
                    answers[idx][label] = checked;
                    // If 'Other' is checked, save the corresponding text
                    const otherCb = group.querySelector('input[type="checkbox"][value="Other"]');
                    if (otherCb && otherCb.checked) {
                        const otherInput = group.querySelector('input[type="text"]');
                        if (otherInput && otherInput.value) {
                            answers[idx][label + ' - Other'] = otherInput.value;
                        }
                    }
                }
            });
            // Save all radio fields (for stage of life)
            if (Number(idx) === 0) {
                const selected = document.querySelector('#category-0 input[type="radio"][name="stage"]:checked');
                if (selected) {
                    answers[0]['Stage of Life'] = selected.parentElement.innerText.trim();
                }
            }
        }
        // --- Navigation logic ---
        const totalCategories = 7;
        let currentCategory = 0;
        function showCategory(idx) {
            for (let i = 0; i < totalCategories; i++) {
                document.getElementById('category-' + i).style.display = (i === idx) ? 'block' : 'none';
            }
            document.getElementById('review-section').style.display = (idx === totalCategories) ? 'block' : 'none';
            document.getElementById('prev-category').style.display = idx > 0 && idx < totalCategories ? 'inline-block' : 'none';
            document.getElementById('next-category').style.display = (idx < totalCategories - 1) ? 'inline-block' : 'none';
            document.getElementById('review-btn').style.display = (idx === totalCategories - 1) ? 'inline-block' : 'none';
        }
        document.getElementById('prev-category').onclick = function() {
            saveCurrentSection(currentCategory);
            if (currentCategory > 0) {
                currentCategory--;
                showCategory(currentCategory);
            }
        };
        document.getElementById('next-category').onclick = function(e) {
            if (e) e.preventDefault();
            // Validate and highlight before moving to next category
            if (!validateCategory(currentCategory, true)) return;
            saveCurrentSection(currentCategory);
            if (currentCategory < totalCategories - 1) {
                currentCategory++;
                showCategory(currentCategory);
            }
        };
        document.getElementById('review-btn').onclick = function() {
            if (!validateCategory(currentCategory, true)) return;
            saveCurrentSection(currentCategory);
            prepareReview();
            currentCategory++;
            showCategory(currentCategory);
        };
        document.getElementById('edit-answers').onclick = function() {
            currentCategory = 0;
            showCategory(currentCategory);
        };
        // On page load
        showCategory(currentCategory);
        // --- Validation and highlighting ---
        function validateCategory(idx, highlight = false) {
            let valid = true;
            const fs = document.getElementById('category-' + idx);
            fs.querySelectorAll('.missing-answer').forEach(el => el.classList.remove('missing-answer'));
            // Validate selects and text inputs
            fs.querySelectorAll('select, input[type="text"]').forEach(function(input) {
                if (input.hasAttribute('required') && !input.value) {
                    if (highlight) input.classList.add('missing-answer');
                    valid = false;
                } else {
                    input.classList.remove('missing-answer');
                }
            });
            // Validate checkboxes (pick N)
            fs.querySelectorAll('.checkbox-group').forEach(function(group) {
                let min = 0;
                if (group.innerText.includes('pick three')) min = 3;
                if (group.innerText.includes('pick two')) min = 2;
                const checkboxes = group.querySelectorAll('input[type="checkbox"]');
                const checked = Array.from(checkboxes).filter(cb => cb.checked);
                if (min > 0 && checked.length < min) {
                    if (highlight) group.classList.add('missing-answer');
                    valid = false;
                } else {
                    group.classList.remove('missing-answer');
                }
                // If 'Other' is checked, require the text
                const otherCb = group.querySelector('input[type="checkbox"][value="Other"]');
                if (otherCb && otherCb.checked) {
                    const otherInput = group.querySelector('input[type="text"]');
                    if (!otherInput.value) {
                        if (highlight) otherInput.classList.add('missing-answer');
                        valid = false;
                    } else {
                        otherInput.classList.remove('missing-answer');
                    }
                }
            });
            // Special case for Stage of Life (category-0)
            if (Number(idx) === 0) {
                const radios = document.querySelectorAll('#category-0 input[type="radio"][name="stage"]');
                const checked = Array.from(radios).some(r => r.checked);
                if (!checked) {
                    if (highlight) document.getElementById('category-0').classList.add('missing-answer');
                    valid = false;
                } else {
                    document.getElementById('category-0').classList.remove('missing-answer');
                }
            }
            if (!valid && highlight) {
                alert('Please answer all required questions in this section before continuing.');
                const first = fs.querySelector('.missing-answer');
                if (first) first.scrollIntoView({behavior: 'smooth'});
            }
            return valid;
        }
        // --- Review logic ---
        function prepareReview() {
            const reviewDiv = document.getElementById('review-content');
            reviewDiv.innerHTML = '';
            let allAnswered = true;
            const categoryLegends = [
                'Select a Stage of Life',
                'Physicality',
                'Income and Personal Finances',
                'Emotional/Spiritual Values',
                'LifeStyle',
                'Profession',
                'Relationships'
            ];
            for (let idx = 0; idx < categoryLegends.length; idx++) {
                let html = `<h4>${categoryLegends[idx]}</h4><ul>`;
                if (answers[idx]) {
                    for (const [qKey, value] of Object.entries(answers[idx])) {
                        let displayValue = '';
                        if (Array.isArray(value)) {
                            displayValue = value.length > 0 ? value.join(', ') : '';
                        } else {
                            displayValue = value;
                        }
                        if (displayValue) {
                            html += `<li>${qKey}: <span class="review-answer">${displayValue}</span></li>`;
                        } else {
                            html += `<li style="color:red;font-weight:bold;">${qKey}: <span style="color:#555;"><em>Not answered</em></span></li>`;
                            allAnswered = false;
                        }
                    }
                }
                html += '</ul>';
                reviewDiv.innerHTML += html;
            }
            if (!allAnswered) {
                reviewDiv.innerHTML += '<div style="color:red;font-weight:bold;">Please answer all required questions before submitting.</div>';
                document.querySelector('.submit-btn').disabled = true;
            } else {
                document.querySelector('.submit-btn').disabled = false;
            }
        }
        // Prevent submit if not all required answered
        document.getElementById('futureself-form').onsubmit = function(e) {
            prepareReview();
            if (document.querySelector('.submit-btn').disabled) {
                e.preventDefault();
                alert('Please answer all required questions before submitting.');
                return false;
            }
        };
        // --- Live update answers on every change ---
        document.addEventListener('input', function(e) {
            // Find which section this input/select/checkbox belongs to
            let fs = e.target.closest('fieldset.category-section');
            if (!fs) return;
            let idx = parseInt(fs.id.replace('category-', ''));
            saveCurrentSection(idx);
        });
        // Helper: Get previous label text
        function getPreviousLabelText(el) {
            // Try to find the closest previous label, even skipping <br> and text nodes
            let prev = el.previousSibling;
            while (prev) {
                if (prev.nodeType === 1 && prev.tagName === 'LABEL') return prev.innerText.trim();
                prev = prev.previousSibling;
            }
            // If not found, try to find the label by traversing up the parent chain
            let parent = el.parentElement;
            while (parent) {
                let labels = parent.querySelectorAll('label');
                for (let i = labels.length - 1; i >= 0; i--) {
                    if (labels[i].compareDocumentPosition(el) & Node.DOCUMENT_POSITION_FOLLOWING) {
                        return labels[i].innerText.trim();
                    }
                }
                parent = parent.parentElement;
            }
            // Fallback: try to extract from name attribute
            if (el.name) {
                return el.name.replace(/^responses\[[^\]]+\]\[|\]$/g, '').replace(/_/g, ' ');
            }
            return '';
        }
        // Helper: Restore answers to section
        function restoreSection(idx) {
            const fs = document.getElementById('category-' + idx);
            if (!fs || !answers[idx]) return;
            fs.querySelectorAll('label, .checkbox-group').forEach(function(labelOrGroup) {
                let qKey = '', value = '';
                if (labelOrGroup.classList.contains('checkbox-group')) {
                    const label = labelOrGroup.previousElementSibling;
                    qKey = label ? label.innerText.trim() : '';
                    value = answers[idx][qKey] || [];
                    const checkboxes = labelOrGroup.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(cb => { cb.checked = value.includes(cb.value); });
                } else {
                    qKey = labelOrGroup.innerText.trim();
                    value = answers[idx][qKey] || '';
                    const input = labelOrGroup.nextElementSibling;
                    if (input && input.tagName === 'SELECT') {
                        input.value = value;
                    } else if (input && input.tagName === 'INPUT') {
                        input.value = value;
                    }
                }
            });
        }
        // --- Validation and highlighting ---
        function validateCategory(idx) {
            // Special case for Stage of Life (category-0): only require one radio checked
            if (Number(idx) === 0) {
                const radios = document.querySelectorAll('#category-0 input[type="radio"][name="stage"]');
                const checked = Array.from(radios).some(r => r.checked);
                if (!checked) {
                    alert('Please select a stage of life before continuing.');
                    document.getElementById('category-0').scrollIntoView({behavior: 'smooth'});
                    return false;
                }
                return true;
            }
            let valid = true;
            const fs = document.getElementById('category-' + idx);
            fs.querySelectorAll('.missing-answer').forEach(el => el.classList.remove('missing-answer'));
            fs.querySelectorAll('label, .checkbox-group').forEach(function(labelOrGroup) {
                let input, isRequired = false, isMissing = false;
                if (labelOrGroup.classList.contains('checkbox-group')) {
                    const checkboxes = labelOrGroup.querySelectorAll('input[type="checkbox"]');
                    const checked = Array.from(checkboxes).filter(cb => cb.checked);
                    isRequired = checkboxes.length > 0 && checkboxes[0].hasAttribute('required');
                    isMissing = isRequired && checked.length === 0;
                } else {
                    input = labelOrGroup.nextElementSibling;
                    if (input && input.tagName === 'SELECT') {
                        isRequired = input.hasAttribute('required');
                        isMissing = isRequired && !input.value;
                    } else if (input && input.tagName === 'INPUT') {
                        isRequired = input.hasAttribute('required');
                        isMissing = isRequired && !input.value;
                    } else {
                        // --- Handle radio buttons ---
                        const radios = labelOrGroup.querySelectorAll('input[type="radio"]');
                        if (radios.length > 0) {
                            label = labelOrGroup;
                            isRequired = Array.from(radios).some(r => r.hasAttribute('required'));
                            const checked = Array.from(radios).find(r => r.checked);
                            value = checked ? checked.value : '';
                            isMissing = isRequired && !value;
                        }
                    }
                }
                if (isMissing) {
                    labelOrGroup.classList.add('missing-answer');
                    valid = false;
                }
            });
            if (!valid) {
                alert('Please answer all required questions in this section before continuing.');
            }
            return valid;
        }
        // Review logic
        function prepareReview() {
            const form = document.getElementById('futureself-form');
            const reviewDiv = document.getElementById('review-content');
            reviewDiv.innerHTML = '';
            let allAnswered = true;
            // Use the answers object instead of DOM fields
            const categoryLegends = [
                'Select a Stage of Life',
                'Physicality',
                'Income and Personal Finances',
                'Emotional/Spiritual Values',
                'LifeStyle',
                'Profession',
                'Relationships'
            ];
            for (let idx = 0; idx < categoryLegends.length; idx++) {
                let html = `<h4>${categoryLegends[idx]}</h4><ul>`;
                if (idx === 0) {
                    // Only show the selected stage
                    const stage = answers[0] && answers[0]['Stage of Life'] ? answers[0]['Stage of Life'] : '';
                    const isMissing = !stage;
                    html += `<li${isMissing ? ' style="color:red;font-weight:bold;"' : ''}>${stage ? stage : '<em>Not answered</em>'}</li>`;
                    if (isMissing) allAnswered = false;
                } else if (answers[idx]) {
                    for (const [qKey, value] of Object.entries(answers[idx])) {
                        let displayValue = '';
                        if (Array.isArray(value)) {
                            displayValue = value.length > 0 ? value.join(', ') : '';
                        } else {
                            displayValue = value;
                        }
                        // Only show questions that have a non-empty answer
                        if (displayValue) {
                            html += `<li>${qKey} <span class="review-answer">${displayValue}</span></li>`;
                        } else {
                            html += `<li style="color:red;font-weight:bold;">${qKey} <span style="color:#555;"><em>Not answered</em></span></li>`;
                            allAnswered = false;
                        }
                    }
                }
                html += '</ul>';
                reviewDiv.innerHTML += html;
            }
            if (!allAnswered) {
                reviewDiv.innerHTML += '<div style="color:red;font-weight:bold;">Please answer all required questions before submitting.</div>';
                document.querySelector('.submit-btn').disabled = true;
            } else {
                document.querySelector('.submit-btn').disabled = false;
            }
        }
        // Prevent submit if not all required answered
        document.getElementById('futureself-form').onsubmit = function(e) {
            prepareReview();
            if (document.querySelector('.submit-btn').disabled) {
                e.preventDefault();
                alert('Please answer all required questions before submitting.');
                return false;
            }
        };
        // Show/hide 'Other' text inputs for select fields
        document.addEventListener('DOMContentLoaded', function() {
            function handleOther(selectClass, inputClass) {
                document.querySelectorAll('select.' + selectClass).forEach(function(select) {
                    select.addEventListener('change', function() {
                        var input = select.parentElement.querySelector('input.' + inputClass);
                        if (select.value === 'Other') {
                            input.style.display = 'inline-block';
                            input.required = true;
                        } else {
                            input.style.display = 'none';
                            input.required = false;
                        }
                    });
                });
            }
            handleOther('hair-colour-select', 'hair-colour-other');
            handleOther('posture-select', 'posture-other');
            handleOther('dress-select', 'dress-other');
            handleOther('income-earn-select', 'income-earn-other');
            handleOther('asset-select', 'asset-other');
            handleOther('saved-select', 'saved-other');
            handleOther('hobbies-select', 'hobbies-other');
            handleOther('sacrifice-select', 'sacrifice-other');
            handleOther('reframe-select', 'reframe-other');
            handleOther('friends-select', 'friends-other');
            handleOther('friends-do-select', 'friends-do-other');
            handleOther('boundaries-select', 'boundaries-other');
            // For multi-selects with Other
            function handleOtherMulti(selectName, inputClass) {
                document.querySelectorAll('select[name^="' + selectName + '"]').forEach(function(select) {
                    select.addEventListener('change', function() {
                        var input = select.parentElement.querySelector('input.' + inputClass);
                        var found = false;
                        for (var i = 0; i < select.options.length; i++) {
                            if (select.options[i].selected && select.options[i].value === 'Other') found = true;
                        }
                        if (found) {
                            input.style.display = 'inline-block';
                            input.required = true;
                        } else {
                            input.style.display = 'none';
                            input.required = false;
                        }
                    });
                });
            }
            handleOtherMulti('responses[Emotional/Spiritual Values][2. What are your core values (pick three)?]', 'core-values-other');
            handleOtherMulti('responses[Emotional/Spiritual Values][3. How will you keep yourself emotionally balanced and healthy (pick three)?]', 'balanced-other');
            handleOtherMulti('responses[Emotional/Spiritual Values][4. How do your core values relate to your financial goal (pick two)?]', 'core-values-relate-other');
        });
        // Show/hide 'Other' text inputs for Emotional/Spiritual Values checkboxes
        document.querySelectorAll('.core-values-other-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                var input = checkbox.parentElement.parentElement.querySelector('input.core-values-other');
                input.style.display = checkbox.checked ? 'inline-block' : 'none';
                input.required = checkbox.checked;
            });
        });
        document.querySelectorAll('.balanced-other-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                var input = checkbox.parentElement.parentElement.querySelector('input.balanced-other');
                input.style.display = checkbox.checked ? 'inline-block' : 'none';
                input.required = checkbox.checked;
            });
        });
        document.querySelectorAll('.core-values-relate-other-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                var input = checkbox.parentElement.parentElement.querySelector('input.core-values-relate-other');
                input.style.display = checkbox.checked ? 'inline-block' : 'none';
                input.required = checkbox.checked;
            });
        });
        // Validate required fields in current category
        function validateCategory(idx) {
            // Special case for Stage of Life (category-0): only require one radio checked
            if (Number(idx) === 0) {
                const radios = document.querySelectorAll('#category-0 input[type="radio"][name="stage"]');
                const checked = Array.from(radios).some(r => r.checked);
                if (!checked) {
                    alert('Please select a stage of life before continuing.');
                    // Highlight the section
                    document.getElementById('category-0').scrollIntoView({behavior: 'smooth'});
                    return false;
                }
                return true;
            }
            let valid = true;
            const fs = document.getElementById('category-' + idx);
            // Remove previous highlights
            fs.querySelectorAll('.missing-answer').forEach(el => el.classList.remove('missing-answer'));
            fs.querySelectorAll('label, .checkbox-group').forEach(function(labelOrGroup) {
                let input, isRequired = false, isMissing = false;
                if (labelOrGroup.classList.contains('checkbox-group')) {
                    const checkboxes = labelOrGroup.querySelectorAll('input[type="checkbox"]');
                    const checked = Array.from(checkboxes).filter(cb => cb.checked);
                    isRequired = checkboxes.length > 0 && checkboxes[0].hasAttribute('required');
                    isMissing = isRequired && checked.length === 0;
                } else {
                    input = labelOrGroup.nextElementSibling;
                    if (input && input.tagName === 'SELECT') {
                        isRequired = input.hasAttribute('required');
                        isMissing = isRequired && !input.value;
                    } else if (input && input.tagName === 'INPUT') {
                        isRequired = input.hasAttribute('required');
                        isMissing = isRequired && !input.value;
                    } else {
                        // --- Handle radio buttons ---
                        const radios = labelOrGroup.querySelectorAll('input[type="radio"]');
                        if (radios.length > 0) {
                            label = labelOrGroup;
                            isRequired = Array.from(radios).some(r => r.hasAttribute('required'));
                            const checked = Array.from(radios).find(r => r.checked);
                            value = checked ? checked.value : '';
                            isMissing = isRequired && !value;
                        }
                    }
                }
                if (isMissing) {
                    labelOrGroup.classList.add('missing-answer');
                    valid = false;
                }
            });
            if (!valid) {
                alert('Please answer all required questions in this section before continuing.');
            }
            return valid;
        }
        // Review logic
        function prepareReview() {
            const form = document.getElementById('futureself-form');
            const reviewDiv = document.getElementById('review-content');
            reviewDiv.innerHTML = '';
            let allAnswered = true;
            // Use the answers object instead of DOM fields
            const categoryLegends = [
                'Select a Stage of Life',
                'Physicality',
                'Income and Personal Finances',
                'Emotional/Spiritual Values',
                'LifeStyle',
                'Profession',
                'Relationships'
            ];
            for (let idx = 0; idx < categoryLegends.length; idx++) {
                let html = `<h4>${categoryLegends[idx]}</h4><ul>`;
                if (idx === 0) {
                    // Only show the selected stage
                    const stage = answers[0] && answers[0]['Stage of Life'] ? answers[0]['Stage of Life'] : '';
                    const isMissing = !stage;
                    html += `<li${isMissing ? ' style="color:red;font-weight:bold;"' : ''}>${stage ? stage : '<em>Not answered</em>'}</li>`;
                    if (isMissing) allAnswered = false;
                } else if (answers[idx]) {
                    for (const [qKey, value] of Object.entries(answers[idx])) {
                        let displayValue = '';
                        if (Array.isArray(value)) {
                            displayValue = value.length > 0 ? value.join(', ') : '';
                        } else {
                            displayValue = value;
                        }
                        // Only show questions that have a non-empty answer
                        if (displayValue) {
                            html += `<li>${qKey} <span class="review-answer">${displayValue}</span></li>`;
                        } else {
                            html += `<li style="color:red;font-weight:bold;">${qKey} <span style="color:#555;"><em>Not answered</em></span></li>`;
                            allAnswered = false;
                        }
                    }
                }
                html += '</ul>';
                reviewDiv.innerHTML += html;
            }
            if (!allAnswered) {
                reviewDiv.innerHTML += '<div style="color:red;font-weight:bold;">Please answer all required questions before submitting.</div>';
                document.querySelector('.submit-btn').disabled = true;
            } else {
                document.querySelector('.submit-btn').disabled = false;
            }
        }
        // Prevent submit if not all required answered
        document.getElementById('futureself-form').onsubmit = function(e) {
            prepareReview();
            if (document.querySelector('.submit-btn').disabled) {
                e.preventDefault();
                alert('Please answer all required questions before submitting.');
                return false;
            }
        };
        // Checkbox limit logic
        function limitCheckboxes(selector, max) {
            document.querySelectorAll(selector).forEach(function(group) {
                group.addEventListener('change', function() {
                    const checkboxes = group.querySelectorAll('input[type="checkbox"]');
                    const checked = Array.from(checkboxes).filter(cb => cb.checked);
                    if (checked.length >= max) {
                        checkboxes.forEach(cb => {
                            if (!cb.checked) cb.disabled = true;
                        });
                    } else {
                        checkboxes.forEach(cb => cb.disabled = false);
                    }
                });
            });
        }
        // For core values (pick three)
        limitCheckboxes('fieldset#category-3 .checkbox-group:nth-of-type(1)', 3);
        // For balanced and healthy (pick three)
        limitCheckboxes('fieldset#category-3 .checkbox-group:nth-of-type(2)', 3);
        // For core values relate (pick two)
        limitCheckboxes('fieldset#category-3 .checkbox-group:nth-of-type(3)', 2);
        // Add redirect for Next button in previous responses section
        document.addEventListener('DOMContentLoaded', function() {
            var prevNextBtn = document.getElementById('next-category');
            if (prevNextBtn && document.querySelector('.future-self-results')) {
                prevNextBtn.addEventListener('click', function() {
                    // Redirect to avatar or next intended page
                    window.location.href = '../generate_avatar/avatar_frontpage.php';
                });
            }
        });
        // After successful form submission, show success message and redirect to review page
        document.getElementById('futureself-form').addEventListener('submit', function(e) {
            if (!document.querySelector('.submit-btn').disabled) {
                document.getElementById('success-message').style.display = 'block';
            }
        });
        </script>
        <?php endif; ?>
    </main>
    <!-- Removed conflicting JS that redirected to avatar section on next-category click -->
    <style>
        .enhanced-intro-card {
            display: flex;
            align-items: flex-start;
            background: #f8fafc;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 2rem 2.5rem;
            margin: 2rem auto 2.5rem auto;
            max-width: 900px;
            gap: 2rem;
        }
        .enhanced-intro-card .intro-icon {
            font-size: 3rem;
            margin-top: 0.5rem;
            color: #00b894;
            flex-shrink: 0;
        }
        .enhanced-intro-card .intro-content {
            flex: 1;
        }
        .enhanced-intro-card h2 {
            margin-top: 0;
            color: #00b894;
            font-weight: 700;
        }
        .enhanced-intro-card ul {
            margin: 0.5em 0 0.5em 1.5em;
            padding-left: 1em;
        }
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1.2em 2em;
            margin: 0.5em 0 1em 0;
        }
        .checkbox-group label {
            font-weight: 400;
            min-width: 180px;
        }
        .checkbox-group input[type="text"] {
            margin-left: 1em;
            min-width: 180px;
        }
        .missing-answer { background: #ffeaea !important; border-left: 4px solid #e53935 !important; }
        .review-category { background: #f8fafc; border-radius: 8px; margin-bottom: 18px; padding: 16px 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .review-list { list-style: none; padding: 0; }
        .review-q { margin-bottom: 8px; }
        .review-question { color: #004085; font-weight: 500; }
        .review-answer { color: #2196f3; font-weight: 500; }
        .missing-review { color: #e53935 !important; font-weight: bold; }
        .review-warning { color: #e53935; font-weight: bold; margin-top: 18px; }
    </style>
</body>
</html>