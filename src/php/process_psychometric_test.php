<!-- filepath: c:\xampp\htdocs\2020FC\src\php\process_psychometric_test.php -->
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare data for the Python script
    $responses = json_encode($_POST);

    // Execute the Python script
    $command = escapeshellcmd("python ../python/psychometric_test.py '$responses'");
    $output = shell_exec($command);

    // Display the results
    echo "<h1>Psychometric Test Results</h1>";
    echo "<pre>$output</pre>";
}
?>