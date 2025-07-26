<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'secure_auth_system');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_secure_password');

// Security configuration
define('SITE_URL', 'https://yourdomain.com'); // Always use HTTPS
define('SESSION_NAME', 'SECURE_SESSION');
define('SESSION_LIFETIME', 3600); // 1 hour
define('CSRF_TOKEN_LIFETIME', 1800); // 30 minutes
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 1800); // 30 minutes in seconds
define('PEPPER', 'your_random_pepper_string'); // Add to password hashes

// Set secure session parameters
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Autoload classes
spl_autoload_register(function ($class) {
    require __DIR__ . '/classes/' . $class . '.php';
});

// Start secure session
new SecureSessionHandler();
session_name(SESSION_NAME);
session_start();

// Regenerate session ID periodically to prevent fixation
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}
?>