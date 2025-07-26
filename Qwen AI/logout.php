<?php
require_once 'auth.php';

// Destroy the session
session_unset();
session_destroy();

// Clear session cookie
setcookie(session_name(), '', [
    'expires' => 1,
    'path' => '/',
    'domain' => '', // Change to your domain
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

header("Location: login.html");
exit;
?>