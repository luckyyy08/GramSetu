<?php
require_once 'config/init.php';
session_destroy();
header("Location: " . APP_URL . "/login.php");
exit();
?>
