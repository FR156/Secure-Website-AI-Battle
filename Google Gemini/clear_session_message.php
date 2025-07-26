<?php
// clear_session_message.php
require_once 'session_helper.php';
start_secure_session();

unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>
