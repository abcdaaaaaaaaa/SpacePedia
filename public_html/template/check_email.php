<?php
session_start();
require_once '../db_config.php';

if(!isset($_SESSION['email_change_user_id'])){
    echo "1";
    exit;
}

$stmt=$db->prepare("SELECT email_change_expires FROM users WHERE id=?");
$stmt->execute([$_SESSION['email_change_user_id']]);
$u=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$u || empty($u['email_change_expires'])){
    echo "1";
    exit;
}

echo "0";
?>
