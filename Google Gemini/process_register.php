<?php
// process_register.php
require_once 'config.php';
require_once 'session_helper.php';
start_secure_session();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    // Input Validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
        $errors[] = "Username must be 3-20 characters long and contain only letters, numbers, and underscores.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Check if username or email already exists (prevent duplicate accounts)
        $stmt_check = $mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        if ($stmt_check === false) {
            error_log("Prepare statement failed: " . $mysqli->error);
            $errors[] = "A server error occurred. Please try again.";
        } else {
            $stmt_check->bind_param("ss", $username, $email);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $errors[] = "Username or Email already exists.";
            }
            $stmt_check->close();
        }
    }

    if (empty($errors)) {
        // Hash the password securely
        // PASSWORD_ARGON2ID is preferred if available (PHP 7.2+), otherwise PASSWORD_BCRYPT
        $password_hash = password_hash($password, PASSWORD_ARGON2ID); // Or PASSWORD_BCRYPT

        // Insert user into database using prepared statement
        $stmt = $mysqli->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        if ($stmt === false) {
            error_log("Prepare statement failed: " . $mysqli->error);
            $errors[] = "A server error occurred during registration. Please try again.";
        } else {
            $stmt->bind_param("sss", $username, $email, $password_hash);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Registration successful! You can now log in.";
                $_SESSION['message_type'] = "success";
                header("Location: login.html");
                exit();
            } else {
                error_log("Execute statement failed: " . $stmt->error);
                $errors[] = "Registration failed. Please try again.";
            }
            $stmt->close();
        }
    }

    // If there are errors, store them in session and redirect back to registration page
    $_SESSION['message'] = implode("<br>", $errors);
    $_SESSION['message_type'] = "error";
    header("Location: register.html");
    exit();
} else {
    // Not a POST request, redirect to registration form
    header("Location: register.html");
    exit();
}
?>