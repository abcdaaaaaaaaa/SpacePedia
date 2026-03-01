<?php
session_start();
require_once '../db_config.php';

if(!isset($_SESSION['resend_login'])){
    echo "0";
    exit;
}

$stmt=$db->prepare("
    SELECT account_closed
    FROM users
    WHERE username=? OR email=?
");
$stmt->execute([
    $_SESSION['resend_login'],
    $_SESSION['resend_login']
]);

$user=$stmt->fetch(PDO::FETCH_ASSOC);

if($user && (int)$user['account_closed']===0){
    echo "1";
}else{
    echo "0";
}
?>
