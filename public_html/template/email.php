<?php
require_once '../db_config.php';
require_once __DIR__.'/mailer.php';

$lang = $_GET['lang'] ?? 'tr';
$token = $_GET['token'] ?? null;

$status = '';
$email_change_link = '../change_email';

if (!$token) {
    $status = $lang === 'tr'
        ? "Geçersiz e-posta değiştirme bağlantısı. Yeni bir bağlantı için <a href='$email_change_link' style='color:blue;'>tıklayınız</a>"
        : "Invalid email change link. Click <a href='$email_change_link' style='color:blue;'>here</a> to request a new one.";
} else {
    $stmt = $db->prepare("
        SELECT id,username,pending_email,email_change_expires 
        FROM users 
        WHERE email_change_token=?
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['pending_email']) {
        $new_email = $user['pending_email'];
        
        if (strtotime($user['email_change_expires']) > time()) {
            $update = $db->prepare("
                UPDATE users 
                SET email=pending_email,
                    pending_email=NULL,
                    email_change_token=NULL,
                    email_change_expires=NULL
                WHERE id=?
            ");
            $update->execute([$user['id']]);
            
            sendEmailChangedSuccessMail($new_email, $user['username'], $lang);

            $status = $lang === 'tr'
                ? "E-posta adresiniz başarıyla değiştirildi."
                : "Your email address has been changed successfully.";
        } else {
            $db->prepare("
                UPDATE users 
                SET pending_email=NULL,
                    email_change_token=NULL,
                    email_change_expires=NULL
                WHERE id=?
            ")->execute([$user['id']]);

            $status = $lang === 'tr'
                ? "E-posta değiştirme bağlantısının süresi doldu. Yeni bir bağlantı için <a href='$email_change_link' style='color:blue;'>tıklayınız</a>."
                : "The email change link has expired. Click <a href='$email_change_link' style='color:blue;'>here</a> to request a new one.";
        }
    } else {
        $status = $lang === 'tr'
            ? "Geçersiz e-posta değiştirme bağlantısı."
            : "Invalid email change link.";
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<title><?php echo $lang === 'tr' ? 'E-Posta Değiştir' : 'Change Email'; ?></title>
<link rel="stylesheet" type="text/css" href="../styles.css">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
</head>
<body>

<div class="container" style="text-align:center;">
<h2><?php echo $lang === 'tr' ? 'E-Posta Değiştir' : 'Change Email'; ?></h2>

<p style="color:white;"><?php echo $status; ?></p>

</div>

</body>
</html>
