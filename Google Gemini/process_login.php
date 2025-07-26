<?php
// process_login.php
require_once 'config.php';
require_once 'session_helper.php';
start_secure_session();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = [];

    // Basic Input Validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        // Fetch user from database using prepared statement
        $stmt = $mysqli->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
        if ($stmt === false) {
            error_log("Prepare statement failed: " . $mysqli->error);
            $errors[] = "A server error occurred. Please try again.";
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                // Verify password against hash
                if (password_verify($password, $user['password_hash'])) {
                    // Password is correct, create session variables
                    session_regenerate_id(true); // Prevent Session Fixation after successful login
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['loggedin'] = true;
                    $_SESSION['last_activity'] = time(); // Record last activity time for session timeout

                    $_SESSION['message'] = "Login successful! Welcome, " . htmlspecialchars($user['username']) . ".";
                    $_SESSION['message_type'] = "success";

                    header("Location: landing.php"); // Redirect to landing page
                    exit();
                } else {
                    $errors[] = "Invalid username or password.";
                }
            } else {
                $errors[] = "Invalid username or password.";
            }
            $stmt->close();
        }
    }

    // If login failed or there are errors, redirect back to login page
    $_SESSION['message'] = implode("<br>", $errors);
    $_SESSION['message_type'] = "error";
    header("Location: login.html");
    exit();
} else {
    // Not a POST request, redirect to login form
    header("Location: login.html");
    exit();
}
?>
