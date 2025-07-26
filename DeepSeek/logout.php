<?php
require_once 'config.php';
require_once 'classes/Auth.php';
require_once 'classes/Utils.php';

$auth = new Auth();
$utils = new Utils();

$auth->logout();
$utils->redirect('/login.php');
?>