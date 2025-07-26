<?php
// config.php
session_start();

// Set session cookie parameters for security
$cookieParams = session_get_cookie_params();
session_set_cookie_params(
    $cookieParams["lifetime"],
    "/",
    $cookieParams["domain"],
    true, // Only use HTTPS
    true // Use SameSite=Strict
);

// Start the session
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');
define('DB_PASSWORD', 'your_db_password');
define('DB_NAME', 'secure_auth');

// Rate limiting configuration
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION_MINUTES', 15);

// CSRF token configuration
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>