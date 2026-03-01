<?php
session_start();
require_once 'db_config.php';

if(isset($_SESSION['user_id'])){
    $db->prepare("UPDATE users SET last_online = NOW() WHERE id = ?")->execute([$_SESSION['user_id']]);
}

session_unset();
session_destroy();
header("Location: index.php");
exit();
?>
