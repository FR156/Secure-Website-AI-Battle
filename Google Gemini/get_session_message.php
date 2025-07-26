<?php
// get_session_message.php
require_once 'session_helper.php';
start_secure_session();

header('Content-Type: application/json');

$response = [
    'message' => $_SESSION['message'] ?? null,
    'type' => $_SESSION['message_type'] ?? null
];

echo json_encode($response);
?>