<?php
session_start();
require_once '../db_config.php';

if (!isset($_SESSION['reset_user_id'])) {
    echo "1";
    exit;
}

$stmt = $db->prepare("SELECT reset_token_expires FROM users WHERE id=?");
$stmt->execute([$_SESSION['reset_user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || empty($user['reset_token_expires'])) {
    echo "1";
    exit;
}

echo "0";
?>
