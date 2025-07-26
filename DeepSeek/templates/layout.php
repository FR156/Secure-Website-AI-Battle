<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Secure Auth System</h1>
            <nav>
                <ul class="flex space-x-4">
                    <?php if (isset($auth) && $auth->isLoggedIn()): ?>
                        <li><a href="/dashboard.php" class="hover:underline">Dashboard</a></li>
                        <li><a href="/logout.php" class="hover:underline">Logout</a></li>
                    <?php else: ?>
                        <li><a href="/login.php" class="hover:underline">Login</a></li>
                        <li><a href="/register.php" class="hover:underline">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="flex-grow container mx-auto px-4 py-8">
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endif; ?>
        
        <?= $content ?>
    </main>
    
    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?= date('Y') ?> Secure Auth System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>