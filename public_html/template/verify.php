<?php
session_start();
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
require_once '../db_config.php';
require_once __DIR__.'/mailer.php';

$token = $_GET['token'] ?? null;

$status = '';
$success = false;
$resend_link = '../resend_activation';

if (!$token) {
    $status = $lang === 'tr'
        ? "Geçersiz aktivasyon bağlantısı. Yeni bir aktivasyon bağlantısı için <a href='$resend_link' style='color:blue;'>tıklayınız</a>."
        : "Invalid activation link. Click <a href='$resend_link' style='color:blue;'>here</a> to request a new activation link.";
} else {
    $stmt = $db->prepare("SELECT id,email_token_expires,email_verified,username,email FROM users WHERE email_token=?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $status = $lang === 'tr'
            ? "Geçersiz aktivasyon bağlantısı. Yeni bir aktivasyon bağlantısı için <a href='$resend_link' style='color:blue;'>tıklayınız</a>."
            : "Invalid activation link. Click <a href='$resend_link' style='color:blue;'>here</a> to request a new activation link.";
    } elseif ($user['email_verified']) {
        $status = $lang === 'tr'
            ? "Bu hesap zaten aktive edilmiş."
            : "This account is already activated.";
        $success = true;
    } elseif (strtotime($user['email_token_expires']) < time()) {
        $status = $lang === 'tr'
            ? "Aktivasyon bağlantısının süresi doldu. Yeni bir aktivasyon bağlantısı için <a href='$resend_link' style='color:blue;'>tıklayınız</a>."
            : "The activation link has expired. Click <a href='$resend_link' style='color:blue;'>here</a> to request a new activation link.";
    } else {
        $db->prepare("UPDATE users SET email_verified=1,email_token=NULL,email_token_expires=NULL WHERE id=?")
           ->execute([$user['id']]);

        sendActivationSuccessMail($user['email'],$user['username'],$lang);

        $status = $lang === 'tr'
            ? "Hesabınız başarıyla aktive edildi."
            : "Your account has been successfully activated.";
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<title><?php echo $lang === 'tr' ? "Hesap Aktivasyonu" : "Account Activation"; ?></title>
<link rel="stylesheet" type="text/css" href="../styles.css">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
</head>
<body>
<div class="container" style="text-align:center;">
    <h2><?php echo $lang === 'tr' ? "Hesap Aktivasyonu" : "Account Activation"; ?></h2>
    <p style="color:white; font-size:16px; margin-top:20px;"><?php echo $status; ?></p>
</div>
</body>
</html>
