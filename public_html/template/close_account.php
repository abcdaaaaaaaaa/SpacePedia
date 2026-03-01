<?php
session_start();
require_once '../db_config.php';
if(!isset($_SESSION['username'])){header("Location:/login");exit;}

if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}
elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}
else{$lang='tr';}

$step=$_POST['step']??'info';
$error='';
$reason=$_POST['close_reason']??'';

if($_SERVER['REQUEST_METHOD']==='POST' && $step==='password'){
$password=$_POST['password']??'';

$stmt=$db->prepare("SELECT password FROM users WHERE username=?");
$stmt->execute([$_SESSION['username']]);
$hash=$stmt->fetchColumn();

if($hash && $password!=='' && password_verify($password,$hash)){
    $db->prepare("UPDATE users SET account_closed=1, account_closed_at=NOW(), account_close_count=account_close_count+1, account_close_reason=?WHERE username=?")->execute([$reason,$_SESSION['username']]);
    session_destroy();
    header("Location:/");
    exit;
}

if($hash && $password!=='' && !password_verify($password,$hash)){
    $error=$lang==='tr'?'Parola hatalı.':'Incorrect password.';
}
}

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $lang==='tr'?'Hesabı Kapat':'Close Account'; ?></title>

<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
html,body{width:100%;height:100%;margin:0;padding:0}
body{display:flex;align-items:center;justify-content:center}
.close-box{max-width:520px;width:100%;padding:34px;background:#4a57c8;border-radius:16px;border:1px solid #7c6cff}
.close-box h1{text-align:center;margin-bottom:18px;color:#eef0ff;font-weight:600}
.close-box p{text-align:left;margin-bottom:26px;color:#e0e3ff;line-height:1.6}
.close-box input{width:100%;box-sizing:border-box;padding:15px;border-radius:8px;border:1px solid #8b7cff;margin-bottom:18px;background:#5c6be6;color:#eef0ff}
.close-box input::placeholder{color:#d6dbff}
.close-box button{width:100%;padding:15px;border-radius:8px;border:none;background:#7a5cff;color:#fff;font-size:16px;cursor:pointer}
.close-box button:hover{background:#6b4bd8}
.error{text-align:center;color:#ffd6ff;margin-bottom:14px}
#togglePassword{height:2.15em}
</style>
</head>
<body>

<div class="close-box">
<h1><?php echo $lang==='tr'?'Hesabı Kapat':'Close Account'; ?></h1>

<?php if($step==='info'){ ?>

<p>
<?php echo $lang==='tr'
?'Bu işlemi gerçekleştirmenizi istemiyoruz.<br>
Bir sorun yaşıyorsanız sizinle ilgilenmek isteriz.<br>
Ancak devam etmeniz halinde bu işlem geri alınamaz.<br>
Kapatılan hesapların herkese açık olarak yayımladığı çalışmalar kapatılmaz.<br>
Hesabınızı /reopen_account sayfasından veya giriş formu üzerinden tekrar açabilirsiniz.<br>
Hesabınızın yeniden kullanılabilmesi için e-posta doğrulaması gereklidir.<br>
Güvenlik nedeniyle bir kullanıcı en fazla 3 kez hesap kapatma işlemi yapabilir.<br>
4. kez kapatılan hesaplar yeniden açılamaz.'
:'We would prefer you not to proceed.<br>
If you are experiencing an issue, we would like to help.<br>
However, if you continue, this action cannot be undone.<br>
Publicly published works of closed accounts will remain accessible.<br>
You can reopen your account via /reopen_account or from the login form.<br>
Email verification is required to reuse your account.<br>
For security reasons, a user may close their account a maximum of 3 times.<br>
Accounts closed for the 4th time cannot be reopened.'; ?>
</p>

<form method="post">
<input type="hidden" name="step" value="reason">
<button type="submit"><?php echo $lang==='tr'?'Devam Et':'Continue'; ?></button>
</form>

<?php } elseif($step==='reason'){ ?>

<form method="post">
<input type="hidden" name="step" value="password">
<input type="text" name="close_reason" maxlength="255"
placeholder="<?php echo $lang==='tr'?'Hesabı kapatma sebebinizi belirtiniz (isteğe bağlı)':'Specify your reason (optional)'; ?>">
<button type="submit"><?php echo $lang==='tr'?'Devam Et':'Continue'; ?></button>
</form>

<?php } elseif($step==='password'){ ?>

<?php if($error!=''){echo'<div class="error">'.$error.'</div>';} ?>

<form method="post">
<input type="hidden" name="step" value="password">
<input type="hidden" name="close_reason" value="<?php echo htmlspecialchars($reason,ENT_QUOTES,'UTF-8'); ?>">

<div style="position:relative;">
<input type="password" name="password" id="password" oninput="checkMaxLength(this);"
placeholder="<?php echo $lang==='tr'?'Parolanızı giriniz.':'Enter your password.'; ?>" required>
<span id="togglePassword" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);color:#888">
<i class="fa-solid fa-eye"></i>
</span>
</div>

<button type="submit"><?php echo $lang==='tr'?'Hesabı Kapat':'Close Account'; ?></button>
</form>

<?php } ?>

</div>

<script>
function checkMaxLength(input){
if(input.value.length>=100){
input.setCustomValidity("<?php echo $lang==='tr'?'Parola en fazla 100 karakter olabilir.':'Password can be max 100 characters.'; ?>");
}else{input.setCustomValidity('');}
}
const togglePassword=document.getElementById("togglePassword");
const passwordInput=document.getElementById("password");
if(togglePassword){
togglePassword.addEventListener("click",()=>{
const icon=togglePassword.querySelector("i");
if(passwordInput.type==="password"){
passwordInput.type="text";
icon.className="fa-solid fa-eye-slash";
}else{
passwordInput.type="password";
icon.className="fa-solid fa-eye";
}
});
}
</script>

</body>
</html>
