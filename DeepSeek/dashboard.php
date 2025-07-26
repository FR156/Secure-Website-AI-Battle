<?php
require_once 'config.php';
require_once 'classes/Auth.php';
require_once 'classes/Utils.php';

$auth = new Auth();
$utils = new Utils();

// Redirect if not logged in
if (!$auth->isLoggedIn()) {
    $utils->redirect('/login.php');
}

$user = $auth->getUser();

ob_start();
?>
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
    <div class="bg-blue-600 py-4 px-6">
        <h2 class="text-xl font-semibold text-white">Welcome, <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>!</h2>
    </div>
    
    <div class="p-6">
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Your Account Information</h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Username</p>
                        <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Email</p>
                        <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex space-x-4">
            <a href="/logout.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Logout
            </a>
            
            <a href="/change_password.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Change Password
            </a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();

$title = "Dashboard";
include 'templates/layout.php';
?>