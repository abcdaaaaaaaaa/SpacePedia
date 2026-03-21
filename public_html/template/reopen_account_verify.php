<?php
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
require_once '../db_config.php';
require_once __DIR__.'/mailer.php';

$lang=$_GET['lang']??'tr';
$token=$_GET['token']??null;

$status='';
$success=false;

if(!$token){
    $status=$lang==='tr'
        ?'Geçersiz bağlantı.'
        :'Invalid link.';
}else{

    $stmt=$db->prepare("
        SELECT id,username,email,
               account_closed,
               account_close_count
        FROM users
        WHERE account_reopen_token=?
    ");
    $stmt->execute([$token]);
    $user=$stmt->fetch(PDO::FETCH_ASSOC);

    if(!$user){
        $status=$lang==='tr'
            ?'Bağlantı geçersiz veya daha önce kullanılmış.'
            :'Invalid or already used link.';
    }
    elseif($user['account_close_count']>=4 || $user['account_closed']==-1){
        $status=$lang==='tr'
            ?'Bu hesap yeniden açılamaz.'
            :'This account cannot be reopened.';
    }
    elseif($user['account_closed']==0){
        $status=$lang==='tr'
            ?'Bu hesap zaten açık.'
            :'This account is already open.';
        $success=true;
    }
    else{
        $db->prepare("
            UPDATE users SET
                account_closed=0,
                account_reopen_token=NULL
            WHERE id=?
        ")->execute([$user['id']]);

        sendAccountReopenSuccessMail($user['email'],$user['username'],$lang);

        $status=$lang==='tr'
            ?'Hesabınız başarıyla yeniden açıldı.'
            :'Your account has been successfully reopened.';
        $success=true;
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<title><?php echo $lang==='tr'?'Hesabı Yeniden Aç':'Reopen Account'; ?></title>
<link rel="stylesheet" type="text/css" href="../styles.css">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
</head>
<body>
<div class="container" style="text-align:center;">
<h2><?php echo $lang==='tr'?'Hesabı Yeniden Aç':'Reopen Account'; ?></h2>
<p style="color:white; font-size:16px; margin-top:20px;"><?php echo $status; ?></p>
</div>
</body>
</html>
