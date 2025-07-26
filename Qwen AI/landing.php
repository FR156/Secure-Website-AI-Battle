<?php
require_once 'auth.php';
requireAuth(); // Ensure user is authenticated

$username = htmlspecialchars($_SESSION['user']['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Welcome</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md text-center">
        <h2 class="text-2xl font-bold mb-4">Welcome, <?= $username ?>!</h2>
        <p class="mb-6">You're successfully logged in.</p>
        
        <a href="logout.php" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
           Logout
        </a>
    </div>
</body>
</html>