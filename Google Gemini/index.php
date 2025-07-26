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
        <?php
            // This is a placeholder. In a real PHP file, you'd check for a session.
            session_start();
            if (isset($_SESSION['username'])) {
                echo '<p class="text-2xl text-gray-700 mb-8">Hello, <span class="font-bold text-blue-600">' . htmlspecialchars($_SESSION['username']) . '</span>!</p>';
            } else {
                echo '<p class="text-2xl text-gray-700 mb-8">You are not logged in. Please <a href="login.html" class="text-blue-600 hover:underline">login</a>.</p>';
            }
        ?>
        <div class="space-y-4">
            <?php if (isset($_SESSION['username'])): ?>
                <a href="logout.php"
                   class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Logout
                </a>
            <?php endif; ?>
            <a href="#"
               class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Explore Features
            </a>
            <!-- Add more navigation options here -->
        </div>
    </div>
</body>
</html>