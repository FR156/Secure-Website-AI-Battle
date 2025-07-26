<?php
require_once "../includes/auth.php";
require_once "../templates/header.php";
require_login();
?>
<h2 class="text-xl font-bold">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
<a href="logout.php" class="inline-block mt-4 text-red-500 underline">Logout</a>
<?php include "../templates/footer.php"; ?>
