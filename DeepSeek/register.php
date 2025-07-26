<?php
require_once 'config.php';
require_once 'classes/Auth.php';
require_once 'classes/Utils.php';

$auth = new Auth();
$utils = new Utils();

$error = '';
$success = '';
$username = '';
$email = '';

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    $utils->redirect('/dashboard.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = $utils->sanitizeInput($_POST['username'] ?? '');
        $email = $utils->sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate inputs
        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            throw new Exception("All fields are required");
        }
        
        if ($password !== $confirmPassword) {
            throw new Exception("Passwords do not match");
        }
        
        $passwordStrength = $utils->validatePasswordStrength($password);
        if ($passwordStrength !== true) {
            throw new Exception($passwordStrength);
        }
        
        // Register user
        $userId = $auth->register($username, $email, $password);
        
        $success = "Registration successful! You can now login.";
        $username = '';
        $email = '';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// CSRF token for registration form
$csrfToken = $auth->generateCSRFToken();

ob_start();
?>
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
    <div class="bg-blue-600 py-4 px-6">
        <h2 class="text-xl font-semibold text-white">Create a New Account</h2>
    </div>
    
    <form method="POST" class="p-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        
        <div class="mb-4">
            <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>" 
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <p class="text-gray-600 text-xs mt-1">3-50 characters, letters and numbers only</p>
        </div>
        
        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" 
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        
        <div class="mb-4">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
            <input type="password" id="password" name="password" 
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <p class="text-gray-600 text-xs mt-1">Minimum 12 characters, with uppercase, lowercase, number, and special character</p>
        </div>
        
        <div class="mb-6">
            <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" 
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-150">
                Register
            </button>
            
            <a href="/login.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Already have an account? Login
            </a>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();

include 'templates/layout.php';
?>