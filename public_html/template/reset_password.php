<?php
session_start();
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
require_once '../db_config.php';
require_once __DIR__.'/mailer.php';

$status_text='';
$error_text='';
$remaining=0;
$expires_at=0;
$show_form=true;
$show_resend=false;

function getUser($db,$username,$email){
    $stmt=$db->prepare("SELECT id,username,email,account_closed,email_verified,reset_token_expires FROM users WHERE username=? AND email=?");
    $stmt->execute([$username,$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['username'],$_POST['email'])){

    $user=getUser($db,$_POST['username'],$_POST['email']);

    if(!$user){
        $error_text=$lang==='tr'
            ?'Kullanıcı adı veya e-posta hatalı.'
            :'Username or email is incorrect.';
    }else{
        
        if($user['account_closed']==1){
            $error_text=$lang==='tr'
                ?'Bu hesap kapatıldığı için şu anda işlem yapılamaz.'
                :'This account is closed, so processing cannot be done at this time.';
        }
        elseif($user['account_closed']==-1){
            $error_text=$lang==='tr'
                ?'Bu Hesap uzay.info Platformunun Kullanım Şartlarını İhlâl Ederek Kapatıldığı için İşlem Yapılamaz.'
                :'This Account Has Been Closed Due to Violation of SpacePedia Platform Terms of Use, So Processing Cannot Be Done.';
        }
        elseif($user['email_verified']==0){
            $error_text=$lang==='tr'
                ?'Hesabınız aktifleştirilmemiş. Lütfen e-postanızı kontrol edin.'
                :'Your account is not activated. Please check your email.';
        }
        elseif($user['reset_token_expires'] && strtotime($user['reset_token_expires']) > time()){
            $error_text=$lang==='tr'
                ?'Zaten aktif bir parola yenileme isteğiniz var. Lütfen sürenin bitmesini bekleyin.'
                :'You already have an active password reset request. Please wait until it expires.';
        }else{

            $token=bin2hex(random_bytes(32));
            $expires_at=time()+600;

            $db->prepare("UPDATE users SET reset_token=?,reset_token_expires=?,last_reset_sent=NOW() WHERE id=?")
               ->execute([$token,date('Y-m-d H:i:s',$expires_at),$user['id']]);

            sendResetMail($user['email'],$user['username'],$token,$lang);

            $_SESSION['reset_user_id']=$user['id'];
            $remaining=600;
            $show_form=false;

            $status_text=$lang==='tr'
                ?"Parola yenileme bağlantısı e-posta adresinize gönderildi."
                :"Password reset link has been sent to your email.";
        }
    }
}

if(isset($_SESSION['reset_user_id'])){
    $stmt=$db->prepare("SELECT reset_token_expires FROM users WHERE id=?");
    $stmt->execute([$_SESSION['reset_user_id']]);
    $u=$stmt->fetch(PDO::FETCH_ASSOC);

    if($u && $u['reset_token_expires']){
        $expires_at=strtotime($u['reset_token_expires']);
        $remaining=$expires_at-time();

        if($remaining>0){
            $show_form=false;

            if(!$status_text){
                $status_text=$lang==='tr'
                    ?"Parola yenileme bağlantısı e-posta adresinize gönderildi."
                    :"Password reset link has been sent to your email.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<title><?php echo $lang==='tr'?'Parola Yenileme':'Password Reset'; ?></title>
<link rel="stylesheet" href="../styles.css">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
</head>
<body>

<div class="container">

<h2 style="text-align:center;">
<?php echo $lang==='tr'?'Parola Yenileme':'Password Reset'; ?>
</h2>

<?php if($show_form): ?>

<form method="post">
<label><?php echo $lang==='tr'?'Kullanıcı Adı:':'Username:'; ?></label><br>
<input type="text" name="username" minlength="4" maxlength="100" required><br>

<label><?php echo $lang==='tr'?'E-Posta:':'E-Mail:'; ?></label><br>
<input type="text" name="email" minlength="4" maxlength="100" required><br>

<?php if($error_text) echo "<p style='color:red;text-align:center;'>$error_text</p>"; ?>

<input type="submit" value="<?php echo $lang==='tr'? "Devam Et" : "Continue"; ?>">
</form>

<?php else: ?>

<p id="status" style="text-align:center;color:white;"><?php echo $status_text; ?></p>
<p id="time" style="text-align:center;color:white;"></p>

<?php endif; ?>

</div>

<script>
const lang="<?php echo $lang; ?>";

let remaining=<?php echo (int)$remaining; ?>;
const expiresAt=<?php echo (int)$expires_at*1000; ?>;

const statusEl=document.getElementById("status");
const timeEl=document.getElementById("time");

let countdownTimer=null;
let checkTimer=null;

const texts={
    remaining:{
        tr:"Kalan süre:",
        en:"Remaining time:"
    },
    success:{
        tr:"Parola başarıyla güncellenmiştir.<br>Ana sayfaya yönlendiriliyorsunuz.",
        en:"Password has been updated successfully.<br>Redirecting to index."
    }
};

function formatTime(sec){
    const m=Math.floor(sec/60);
    const s=sec%60;
    if(lang==="tr"){return m+"dk "+s+"sn";}
    else{return m+"min "+s+"sec";}
}

function startCountdown(){
    if(expiresAt<=0)return;

    countdownTimer=setInterval(()=>{
        const diff=Math.floor((expiresAt-Date.now())/1000);
        remaining=diff>0?diff:0;

        if(remaining>0){
            timeEl.textContent=texts.remaining[lang]+" "+formatTime(remaining);
        }else{
            clearInterval(countdownTimer);
            timeEl.textContent="";
            statusEl.innerHTML=lang==="tr"
                ?"Parola yenileme süreniz sona erdi.<br>>Ana sayfaya yönlendiriliyorsunuz."
                :"Your password reset time has expired.<br>Redirecting to index.";
            setTimeout(()=>{
                window.location.href="/logout";
            },2000);
        }
    },1000);
}

function checkPassword(){
    fetch("check_password")
        .then(r=>r.text())
        .then(res=>{
            if(res.trim()==="1"){
                if(countdownTimer)clearInterval(countdownTimer);
                if(checkTimer)clearInterval(checkTimer);

                timeEl.textContent="";
                statusEl.innerHTML=texts.success[lang];

                setTimeout(()=>{
                    window.location.href="/logout";
                },2000);
            }
        });
}

if(remaining>0){
    startCountdown();
    checkTimer=setInterval(checkPassword,2000);
}
</script>

</body>
</html>
