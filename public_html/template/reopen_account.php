<?php
session_start();
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
require_once '../db_config.php';
require_once __DIR__.'/mailer.php';

$lang = $_SESSION['lang'] ?? 'tr';

$status = '';
$error = '';
$step = $_POST['step'] ?? 'login';

function getUserByLogin($db,$login){
    $stmt=$db->prepare("
        SELECT id,username,email,password,
               account_closed,
               account_close_count,
               register_ip
        FROM users
        WHERE username=? OR email=?
    ");
    $stmt->execute([$login,$login]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function activeLimitReached($db,$ip){
    if($ip===null||$ip==='') return false;
    $stmt=$db->prepare("SELECT COUNT(*) FROM users WHERE register_ip=? AND account_closed=0");
    $stmt->execute([$ip]);
    return (int)$stmt->fetchColumn()>=3;
}

if($_SERVER['REQUEST_METHOD']==='POST' && $step==='login'){
    $login=$_POST['login']??'';
    $password=$_POST['password']??'';

    $user=getUserByLogin($db,$login);

    if(!$user || !password_verify($password,$user['password'])){
        $error=$lang==='tr'
            ?'Kullanıcı adı, e-posta veya parola hatalı.'
            :'Username, email or password is incorrect.';
    }
    elseif($user['account_closed']==-1){
        $error=$lang==='tr'
            ?'Bu Hesap SpacePedia/Uzay Platformunun Kullanım Şartlarını İhlâl Ettiği için Yeniden Açılamaz.'
            :'This Account Cannot Be Reopened Because It Has Violated The SpacePedia/Uzay Platform Terms of Use.';
    }
    elseif($user['account_close_count']>=4){
        $error=$lang==='tr'
            ?'Bu hesap 4 kez kapatıldığı için yeniden açılamaz.'
            :'This account cannot be reopened because it has been closed 4 times.';
    }
    elseif(activeLimitReached($db,$user['register_ip'])){
        $status=$lang==='tr'
            ?'Bu cihazdan en fazla 3 hesap aktif olabilir.'
            :'A maximum of 3 accounts can be active on this device.';
    }
    elseif($user['account_closed']==0){
        $status=$lang==='tr'
            ?'Bu hesap zaten açık. Ana sayfaya yönlendiriliyorsunuz.'
            :'This account is already open. Redirecting to index.';
        header("Refresh:2; url=/logout");
    }
    else{
        $token=bin2hex(random_bytes(32));

        $db->prepare("
            UPDATE users SET
                account_reopen_token=?,
                last_reopen_sent=NOW()
            WHERE id=?
        ")->execute([$token,$user['id']]);

        $_SESSION['resend_login']=$login;

        sendAccountReopenMail($user['email'],$user['username'],$token,$lang);

        $status=$lang==='tr'
            ?'Hesabınızı yeniden açılması için e-posta adresinize bir bağlantı gönderildi.'
            :'A reopen link has been sent to your email address.';
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<head>
<meta charset="UTF-8">
<title><?php echo $lang==='tr'?'Hesabı Yeniden Aç':'Reopen Account'; ?></title>
<link rel="stylesheet" type="text/css" href="../styles.css">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
</head>
<body>

<div class="container">

<h2 style="text-align:center;">
<?php echo $lang==='tr'?'Hesabı Yeniden Aç':'Reopen Account'; ?>
</h2>

<?php if($status): ?>
<p style="color:white;text-align:center;"><?php echo $status; ?></p>
<?php else: ?>

<form method="post">
<input type="hidden" name="step" value="login">

<label><?php echo $lang==='tr'?'Kullanıcı Adı veya E-Posta:':'Username or E-Mail:'; ?></label><br>
<input type="text" name="login" required><br>

<label><?php echo $lang==='tr'?'Parola:':'Password:'; ?></label><br>
<div style="position:relative;">
    <input type="password" id="password" name="password" oninput="checkMaxLength(this);" required>
    <span id="togglePassword" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); color:#888;"><i class="fa-solid fa-eye"></i></span>
</div>

<?php if($error): ?>
<p style="color:red;text-align:center;"><?php echo $error; ?></p>
<?php endif; ?>

<input type="submit" value="<?php echo $lang==='tr'?'Devam Et':'Continue'; ?>">
</form>

<?php endif; ?>

</div>

<script>
let checkTimer=null;

const texts={
    tr:{
        activated:"Hesabınız yeniden açılmıştır.<br>Ana sayfaya yönlendiriliyorsunuz."
    },
    en:{
        activated:"Your account has been reactivated.<br>You are being redirected to the homepage."
    }
};

const lang="<?php echo $lang; ?>";

const statusEl=document.querySelector(".container p");

function checkActivation(){
    fetch("check_reopen")
        .then(r=>r.text())
        .then(res=>{
            if(res.trim()==="1"){
                if(statusEl){
                    statusEl.innerHTML=texts[lang].activated;
                }
                clearInterval(checkTimer);
                setTimeout(()=>{
                    window.location.href="/logout";
                },2000);
            }
        });
}

checkTimer=setInterval(checkActivation,3000);

function checkMaxLength(input){
    if(input.value.length>=100){
        input.setCustomValidity("<?php echo $lang==='tr'
            ?'Parola en fazla 100 karakter olabilir.'
            :'Password can be max 100 characters.'; ?>");
    }else{
        input.setCustomValidity('');
    }
}

const togglePassword=document.getElementById("togglePassword");
const passwordInput=document.getElementById("password");

if(togglePassword && passwordInput){
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
