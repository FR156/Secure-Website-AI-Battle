<?php
require_once 'config.php';
require_once 'classes/Auth.php';
require_once 'classes/Utils.php';

$auth = new Auth();
$utils = new Utils();

$error = '';
$username = '';

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    $utils->redirect('/dashboard.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = $utils->sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            throw new Exception("Username and password are required");
        }
        
        if ($auth->login($username, $password)) {
            $utils->redirect('/dashboard.php');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// CSRF token for login form
$csrfToken = $auth->generateCSRFToken();

ob_start();
?>
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
    <div class="bg-blue-600 py-4 px-6">
        <h2 class="text-xl font-semibold text-white">Login to Your Account</h2>
    </div>
    
    <form method="POST" class="p-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        
        <div class="mb-4">
            <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>" 
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        
        <div class="mb-6">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
            <input type="password" id="password" name="password" 
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-150">
                Login
            </button>
            
            <a href="/register.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Don't have an account? Register
            </a>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();

include 'templates/layout.php';
?>