<?php
require_once 'config.php';

function dbConnect() {
    try {
        $pdo = new PDO(
            "mysql:host=".DB_HOST.";dbname=".DB_NAME,
            DB_USER,
            DB_PASSWORD,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        die("A database error occurred. Please try again later.");
    }
}

function requireAuth() {
    if (!isset($_SESSION['user'])) {
        header("Location: login.html");
        exit;
    }
    
    // Check if session has expired
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        // last activity was more than 30 minutes ago
        session_unset();
        session_destroy();
        header("Location: login.html");
        exit;
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
}

function validateInput($input) {
    // Sanitize input to prevent XSS
    $input = htmlspecialchars(strip_tags(trim($input)));
    return $input;
}

function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        throw new Exception("Invalid CSRF token");
    }
}

function isAccountLocked($pdo, $username) {
    $stmt = $pdo->prepare("SELECT locked_until FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $result = $stmt->fetch();
    
    if ($result && strtotime($result['locked_until']) > time()) {
        return true;
    }
    return false;
}

function recordFailedAttempt($pdo, $username) {
    $stmt = $pdo->prepare("UPDATE users SET 
                           failed_login_attempts = failed_login_attempts + 1,
                           locked_until = CASE 
                               WHEN failed_login_attempts + 1 >= ? 
                               THEN DATE_ADD(NOW(), INTERVAL ? MINUTE)
                               ELSE locked_until END
                           WHERE username = ?");
    $stmt->execute([MAX_LOGIN_ATTEMPTS, LOCKOUT_DURATION_MINUTES, $username]);
}

function resetFailedAttempts($pdo, $username) {
    $stmt = $pdo->prepare("UPDATE users SET 
                           failed_login_attempts = 0,
                           locked_until = NULL
                           WHERE username = ?");
    $stmt->execute([$username]);
}
?>