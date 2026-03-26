<?php
session_start();
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}require_once '../db_config.php';
require_once __DIR__ . '/mailer.php';

$status_text = '';
$error_text = '';
$remaining = 0;
$expires_at = 0;
$show_form = false;
$show_resend = false;

function getUser($db, $login) {
    $stmt = $db->prepare("SELECT id,username,email,password,email_verified,email_token_expires,account_closed FROM users WHERE username=? OR email=?");
    $stmt->execute([$login, $login]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!isset($_SESSION['resend_login'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'], $_POST['password'])) {
        $login = $_POST['login'];
        $password = $_POST['password'];

        $user = getUser($db, $login);

        if (!$user || !password_verify($password, $user['password'])) {
            $error_text = $lang === 'tr'
                ? "Kullanıcı adı, e-posta veya parola hatalı."
                : "Username, email, or password is incorrect.";
            $show_form = true;
        } else {
            if($user['account_closed']==1){
                $error_text = $lang === 'tr'
                    ? "Bu hesap kapatılmıştır."
                    : "This account is closed.";
                $show_form = true;
            }
            elseif($user['account_closed']==-1){
                $error_text = $lang === 'tr'
                    ? "Bu Hesap uzay.info Platformunun Kullanım Şartlarını İhlâl Ettiği için Kapatılmıştır."
                    : "This Account Has Been closed Due to Violation of SpacePedia Platform Terms of Use.";
                $show_form = true;
            }
            elseif($user['email_verified']==1){
                $status_text = $lang === 'tr'
                    ? "Bu hesap zaten aktive edilmiş. Ana sayfaya yönlendiriliyorsunuz."
                    : "This account is already activated. Redirecting to index.";
                header("Refresh:2; url=/logout");
            }
            else{
                $_SESSION['resend_login'] = $login;
            }
        }
    } else {
        $show_form = true;
    }
}

if (isset($_SESSION['resend_login'])) {
    $user = getUser($db, $_SESSION['resend_login']);

    if (!$user) {
        session_destroy();
        $show_form = true;
    } elseif ((int)$user['account_closed']===1) {
        $status_text = $lang === 'tr'
            ? "Bu hesap kapatılmıştır."
            : "This account is closed.";
        session_destroy();
    } elseif ((int)$user['account_closed']===-1) {
        $status_text = $lang === 'tr'
            ? "Bu Hesap uzay.info Platformunun Kullanım Şartlarını İhlâl Ettiği için Kapatılmıştır."
            : "This Account Has Been closed Due to Violation of SpacePedia Platform Terms of Use.";
        session_destroy();
    } elseif ($user['email_verified']) {
        $status_text = $lang === 'tr'
            ? "Bu hesap zaten aktive edilmiş. Ana sayfaya yönlendiriliyorsunuz."
            : "This account is already activated. Redirecting to index.";
        header("Refresh:2; url=/logout");
    } else {
        if ($user['email_token_expires']) {
            $expires_at = strtotime($user['email_token_expires']);
            $remaining = $expires_at - time();
            if ($remaining <= 0) {
                $remaining = 0;
                $status_text = $lang === 'tr'
                    ? "Yeni aktivasyon bağlantısı ile hesabınızı aktifleştirmek için aşağıdaki butona tıklayınız."
                    : "To activate your account with the new activation link, click the button below.";
                $show_resend = true;
            }
        } else {
            $show_resend = true;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend'], $_SESSION['resend_login'])) {

    $user = getUser($db, $_SESSION['resend_login']);

    if ($user && !$user['email_verified'] && (int)$user['account_closed']===0) {

        if ($user['email_token_expires'] && strtotime($user['email_token_expires']) > time()) {
            $status_text = $lang === 'tr'
                ? "Zaten aktif bir aktivasyon bağlantısı gönderildi. Lütfen sürenin bitmesini bekleyin."
                : "An activation link has already been sent. Please wait until it expires.";
            $show_resend = false;
        } else {

            $token = bin2hex(random_bytes(32));
            $expires_at = time() + 300;

            $db->prepare("UPDATE users SET email_token=?, email_token_expires=?, last_activation_sent=NOW() WHERE id=?")
               ->execute([$token, date('Y-m-d H:i:s', $expires_at), $user['id']]);

            sendActivationMail($user['email'], $user['username'], $token, $lang);

            $remaining = 300;
            $status_text = $lang === 'tr'
                ? "Yeni aktivasyon bağlantısı e-posta adresinize gönderildi."
                : "A new activation link has been sent to your email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<head>
<meta charset="UTF-8">
<title><?php echo $lang === 'tr' ? "Hesap Aktivasyonu" : "Account Activation"; ?></title>
<link rel="stylesheet" type="text/css" href="../styles.css">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
</head>
<body>

<div class="container">

<h2 style="text-align:center;">
<?php echo $lang === 'tr' ? "Hesap Aktivasyonu" : "Account Activation"; ?>
</h2>

<?php if ($show_form): ?>

<form method="post">
<label><?php echo $lang === 'tr' ? "Kullanıcı Adı veya E-Posta:" : "Username or E-Mail:"; ?></label><br>
<input type="text" name="login" minlength="4" maxlength="100" required><br>

<label><?php echo $lang === 'tr' ? "Parola:" : "Password:"; ?></label><br>
<div style="position:relative;">
    <input type="password" id="password" name="password" oninput="checkMaxLength(this);" required>
    <span id="togglePassword" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); color:#888;"><i class="fa-solid fa-eye"></i></span>
</div>

<?php if($error_text) echo "<p style='color:red;text-align:center;'>$error_text</p>"; ?>

<input type="submit" value="<?php echo $lang === 'tr' ? "Devam Et" : "Continue"; ?>">
</form>

<?php else: ?>

<p id="status" style="text-align:center;color:white;"><?php echo $status_text; ?></p>

<form method="post" id="resendForm" style="display:<?php echo $show_resend ? 'block':'none'; ?>;">
<input type="hidden" name="resend" value="1">
<input type="submit" value="<?php echo $lang === 'tr' ? "Yeni Aktivasyon Bağlantısı Gönder" : "Send New Activation Link"; ?>">
</form>

<?php endif; ?>

</div>

<script>
const lang="<?php echo $lang; ?>";

let remaining=<?php echo (int)$remaining; ?>;
const expiresAt=<?php echo (int)$expires_at*1000; ?>;

const statusEl=document.getElementById("status");
const resendForm=document.getElementById("resendForm");

const texts={
    sent:{
        tr:"Yeni aktivasyon bağlantısı e-posta adresinize gönderildi.<br><br>Kalan süre:",
        en:"A new activation link has been sent to your email.<br><br>Remaining time:"
    },
    expired:{
        tr:"E-posta adresinize gönderilen aktivasyon bağlantısının süresi doldu. Yeni bir aktivasyon bağlantısı için aşağıdaki butona tıklayınız.",
        en:"The activation link sent to your email has expired. Click the button below for a new activation link."
    },
    activated:{
        tr:"Hesap aktive edilmiştir.<br>Ana sayfaya yönlendiriliyorsunuz.",
        en:"Account has been activated.<br>Redirecting to index."
    }
};

function formatTime(sec){
    const m=Math.floor(sec/60);
    const s=sec%60;
    if(lang==="tr"){return m+"dk "+s+"sn";}
    else{return m+"min "+s+"sec";}
}

let timer=null;
let checkTimer=null;

function startCountdown(){
    if(expiresAt<=0)return;

    timer=setInterval(()=>{
        const diff=Math.floor((expiresAt-Date.now())/1000);
        remaining=diff>0?diff:0;

        if(remaining>0){
            statusEl.innerHTML=texts.sent[lang]+" "+formatTime(remaining);
            resendForm.style.display="none";
        }else{
            clearInterval(timer);
            statusEl.textContent=texts.expired[lang];
            resendForm.style.display="block";
        }
    },1000);
}

function checkActivation(){
    fetch("check_activation")
        .then(r=>r.text())
        .then(res=>{
            if(res.trim()==="1"){
                if(timer)clearInterval(timer);
                if(checkTimer)clearInterval(checkTimer);

                statusEl.innerHTML=texts.activated[lang];
                resendForm.style.display="none";

                setTimeout(()=>{
                    window.location.href="/logout";
                },2000);
            }
        });
}

if(remaining>0){
    startCountdown();
    checkTimer=setInterval(checkActivation,2000);
}

function checkMaxLength(input){
    if(input.value.length>=100){
        input.setCustomValidity("<?php echo $lang==='tr'
            ?'Parola en fazla 100 karakter olabilir.'
            :'Password can be max 100 characters.'; ?>");
    } else {
        input.setCustomValidity('');
    }
}

const togglePassword=document.getElementById("togglePassword");
const passwordInput=document.getElementById("password");
togglePassword.addEventListener("click",()=>{
    const icon=togglePassword.querySelector("i");
    if(passwordInput.type==="password"){
        passwordInput.type="text";
        icon.className="fa-solid fa-eye-slash";
    } else {
        passwordInput.type="password";
        icon.className="fa-solid fa-eye";
    }
});
</script>

</body>
</html>
