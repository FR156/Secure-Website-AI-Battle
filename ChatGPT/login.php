<?php
require_once "../includes/db.php";
require_once "../includes/auth.php";
require_once "../includes/csrf.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!verify_csrf_token($_POST['csrf_token'])) die("CSRF validation failed.");

    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
    } else {
        echo "Invalid login.";
    }
}
?>
<?php require_once "../includes/csrf.php"; include "../templates/header.php"; ?>
<h2 class="text-xl font-bold mb-4">Login</h2>
<form method="POST" action="login.php">
    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
    <input name="username" required type="text" placeholder="Username" class="mb-2 p-2 w-full border rounded">
    <input name="password" required type="password" placeholder="Password" class="mb-2 p-2 w-full border rounded">
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Login</button>
</form>
<?php include "../templates/footer.php"; ?>
