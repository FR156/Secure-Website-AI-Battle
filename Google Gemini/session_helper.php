<?php
// session_helper.php

function start_secure_session() {
    // Set session cookie parameters for security
    session_set_cookie_params([
        'lifetime' => 3600, // Session duration in seconds (e.g., 1 hour)
        'path' => '/', // Available across the entire domain
        'domain' => $_SERVER['HTTP_HOST'], // Your domain
        'secure' => true, // Transmit cookie only over HTTPS (CRITICAL)
        'httponly' => true, // Prevent JavaScript access to the cookie (CRITICAL)
        'samesite' => 'Lax' // Protect against CSRF attacks (Strict or Lax are options)
    ]);

    // Start the session
    session_start();

    // Prevent Session Fixation by regenerating session ID on every request or login
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true); // Regenerate ID and delete old session file
        $_SESSION['initiated'] = true;
    }

    // Optional: Session hijacking prevention - check user agent and IP (can cause issues with proxies)
    // For more robust solutions, consider token-based authentication or advanced session management libraries.
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    } elseif ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_unset();
        session_destroy();
        header('Location: login.html?message=Session hijacking attempt detected.');
        exit();
    }
}

// Call this function at the beginning of every PHP script that needs session management
// e.g., require_once 'session_helper.php'; start_secure_session();
?>
