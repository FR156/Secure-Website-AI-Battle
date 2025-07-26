<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate CSRF token
        verifyCSRFToken($_POST['csrf_token']);
        
        // Validate inputs
        $username = validateInput($_POST['username']);
        $email = filter_var(validateInput($_POST['email']), FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if (!$email) {
            throw new Exception("Please enter a valid email address.");
        }
        
        if ($password !== $confirmPassword) {
            throw new Exception("Passwords do not match.");
        }
        
        if (strlen($password) < 8) {
            throw new Exception("Password must be at least 8 characters long.");
        }
        
        $pdo = dbConnect();
        
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            throw new Exception("Username or email already exists.");
        }
        
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $passwordHash]);
        
        // Get the newly created user
        $userId = $pdo->lastInsertId();
        
        // Start session
        session_regenerate_id(true); // Prevent session fixation
        
        $_SESSION['user'] = [
            'id' => $userId,
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
        http_response_code(400);
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
} else {
    header("Location: register.html");
    exit;
}
?>