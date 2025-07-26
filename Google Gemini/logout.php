<?php
// logout.php
require_once 'session_helper.php';
start_secure_session();

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

$_SESSION['message'] = "You have been successfully logged out.";
$_SESSION['message_type'] = "info";

header("Location: login.html"); // Redirect to login page
exit();
?>