<?php
session_start();
if(!isset($_SESSION['username'])){header("Location:/login");exit;}
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $lang==='tr'?'Ayarlar':'Settings'; ?></title>
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
html,body{width:100%;height:100%;margin:0;padding:0;overflow:auto}
body{display:flex;align-items:center;justify-content:center}
.settings-box{max-width:520px;width:100%;padding:36px;background:#4a57c8;border-radius:16px;border:1px solid #7c6cff}
.settings-box h1{text-align:center;margin-bottom:34px;color:#eef0ff;font-weight:600}
.settings-box a{display:flex;align-items:center;gap:14px;padding:18px 20px;margin-bottom:18px;border-radius:12px;text-decoration:none;color:#eef0ff;background:#5c6be6;border:1px solid #8b7cff}
.settings-box a:hover{background:#6a78ff}
.settings-box a i{color:#e0e3ff;font-size:17px}
.settings-box .danger{background:#6b4bd8;border-color:#a58cff}
.settings-box .danger:hover{background:#7a5cff}
</style>
</head>
<body>

<div class="settings-box">
<h1><?php echo $lang==='tr'?'Hesap Ayarları':'Account Settings'; ?></h1>

<a href="/change_email"><i class="fa-solid fa-envelope"></i><?php echo $lang==='tr'?'E-Posta Değiştir':'Change Email'; ?></a>
<a href="/reset_password"><i class="fa-solid fa-key"></i><?php echo $lang==='tr'?'Parola Yenileme':'Password Reset'; ?></a>
<a href="/close_account" class="danger"><i class="fa-solid fa-triangle-exclamation"></i><?php echo $lang==='tr'?'Hesabı Kapat':'Close Account'; ?></a>
</div>

</body>
</html>
