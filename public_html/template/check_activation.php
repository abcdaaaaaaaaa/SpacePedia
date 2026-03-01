<?php
session_start();
require_once '../db_config.php';

if (!isset($_SESSION['resend_login'])) {
    echo "0";
    exit;
}

$stmt = $db->prepare("SELECT email_verified FROM users WHERE username=? OR email=?");
$stmt->execute([$_SESSION['resend_login'],$_SESSION['resend_login']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo ($user && $user['email_verified'] == 1) ? "1" : "0";
?>