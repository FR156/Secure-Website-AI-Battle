<?php
// config.php
// IMPORTANT: In a production environment, use environment variables or a secure configuration mechanism.
// DO NOT hardcode sensitive information directly in files accessible by the web server.

define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Replace with your MariaDB username
define('DB_PASSWORD', 'your_password'); // Replace with your MariaDB password
define('DB_NAME', 'secure_app_db');

// Attempt to connect to the database
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    // Log the error securely, do not display sensitive details to the user
    error_log("Database connection failed: " . $mysqli->connect_error);
    die("A database error occurred. Please try again later.");
}

// Set character set to UTF-8
$mysqli->set_charset("utf8mb4");

// For production, ensure error reporting is turned off for security
ini_set('display_errors', 'Off');
ini_set('log_errors', 'On');
ini_set('error_log', '/path/to/your/php-error.log'); // Specify a secure, non-web-accessible log file path

?>
