<?php
require_once "../includes/db.php";
require_once "../includes/csrf.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!verify_csrf_token($_POST['csrf_token'])) die("CSRF validation failed.");

    if ($_POST['password'] !== $_POST['confirm_password']) {
        die("Passwords do not match.");
    }

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([
        $_POST['username'],
        $_POST['email'],
        password_hash($_POST['password'], PASSWORD_DEFAULT)
    ]);

    header("Location: login.php");
}
?>
<?php require_once "../includes/csrf.php"; include "../templates/header.php"; ?>
<h2 class="text-xl font-bold mb-4">Register</h2>
<form method="POST" action="register.php">
    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
    <input name="username" required type="text" placeholder="Username" class="mb-2 p-2 w-full border rounded">
    <input name="email" required type="email" placeholder="Email" class="mb-2 p-2 w-full border rounded">
    <input name="password" required type="password" placeholder="Password" class="mb-2 p-2 w-full border rounded">
    <input name="confirm_password" required type="password" placeholder="Confirm Password" class="mb-2 p-2 w-full border rounded">
    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Register</button>
</form>
<?php include "../templates/footer.php"; ?>
