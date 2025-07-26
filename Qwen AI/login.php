<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate CSRF token
        verifyCSRFToken($_POST['csrf_token']);
        
        // Validate inputs
        $username = validateInput($_POST['username']);
        $password = $_POST['password'];
        
        $pdo = dbConnect();
        
        // Prevent brute force attacks
        if (isAccountLocked($pdo, $username)) {
            throw new Exception("Account is temporarily locked due to too many failed attempts. Please try again later.");
        }
        
        // Fetch user from database
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // Record failed attempt
            recordFailedAttempt($pdo, $username);
            throw new Exception("Invalid username or password.");
        }
        
        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            // Record failed attempt
            recordFailedAttempt($pdo, $username);
            throw new Exception("Invalid username or password.");
        }
        
        // Reset failed attempts on successful login
        resetFailedAttempts($pdo, $username);
        
        // Start session
        session_regenerate_id(true); // Prevent session fixation
        
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $username
        ];
        
        // Set secure session cookie
        setcookie(session_name(), session_id(), [
            'expires' => 0,
            'path' => '/',
            'domain' => '', // Change to your domain
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        
        // Redirect to landing page
        header("Location: landing.php");
        exit;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
} else {
    header("Location: login.html");
    exit;
}
?>