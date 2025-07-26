<?php
// landing.php
require_once 'session_helper.php';
start_secure_session();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['message'] = "Please log in to access this page.";
    $_SESSION['message_type'] = "error";
    header("Location: login.html");
    exit();
}

// Session timeout mechanism
$inactive_timeout = 1800; // 30 minutes (in seconds)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive_timeout)) {
    session_unset();
    session_destroy();
    $_SESSION['message'] = "Your session has expired due to inactivity. Please log in again.";
    $_SESSION['message_type'] = "info";
    header("Location: login.html");
    exit();
}
$_SESSION['last_activity'] = time(); // Update last activity time
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Secure App</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Optional: Custom font */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-lg text-center">
        <h2 class="text-4xl font-extrabold text-gray-800 mb-6">Welcome!</h2>
        <p class="text-2xl text-gray-700 mb-8">Hello, <span class="font-bold text-blue-600"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!</p>
        <div class="space-y-4">
            <a href="logout.php"
               class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Logout
            </a>
            <a href="#"
               class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Explore Features
            </a>
            <!-- Add more navigation options here -->
        </div>
    </div>
</body>
</html>
