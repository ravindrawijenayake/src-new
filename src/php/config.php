<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'user_reg_db');

// Function to establish a database connection
function getDatabaseConnection() {
    static $conn = null;

    if ($conn === null) {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

            if ($conn->connect_error) {
                throw new Exception("Database connection failed: " . $conn->connect_error);
            }

            // Set character encoding for proper data handling
            $conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            error_log($e->getMessage(), 3, __DIR__ . '/error_log.txt'); // Log errors to a file
            die("We are experiencing technical difficulties. Please try again later."); // User-friendly message
        }
    }

    return $conn;
}

// Function to close the database connection
function closeDatabaseConnection() {
    $conn = getDatabaseConnection();
    if ($conn) {
        $conn->close();
    }
}

// Register shutdown function to ensure the connection is closed
register_shutdown_function('closeDatabaseConnection');
?>