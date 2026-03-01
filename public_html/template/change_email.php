<?php
session_start();
require_once '../db_config.php';
require_once __DIR__.'/mailer.php';

$lang=$_SESSION['lang'] ?? 'tr';

$status_text='';
$error_text='';
$remaining=0;
$expires_at=0;
$show_form=true;

function getUser($db,$username,$password){
    $stmt=$db->prepare("SELECT id,password,email_change_expires FROM users WHERE username=?");
    $stmt->execute([$username]);
    $u=$stmt->fetch(PDO::FETCH_ASSOC);
    if($u && password_verify($password,$u['password'])){
        return $u;
    }
    return null;
}

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['username'],$_POST['password'],$_POST['pending_email'])){

    $user=getUser($db,$_POST['username'],$_POST['password']);

    if(!$user){
        $error_text=$lang==='tr'
            ?'Kullanıcı adı veya parola hatalı.'
            :'Username or password is incorrect.';
    }else{

        if($user['email_change_expires'] && strtotime($user['email_change_expires']) > time()){
            $expires_at=strtotime($user['email_change_expires']);
            $remaining=$expires_at-time();
            $show_form=false;
        }else{

            $token=bin2hex(random_bytes(32));
            $expires_at=time()+600;

            $db->prepare("UPDATE users SET email_change_token=?,email_change_expires=?,pending_email=?,last_email_change_sent=NOW() WHERE id=?")
               ->execute([$token,date('Y-m-d H:i:s',$expires_at),$_POST['pending_email'],$user['id']]);

            sendEmailChangeMail($_POST['pending_email'],$user['username'],$token,$lang);

            $_SESSION['email_change_user_id']=$user['id'];
            $remaining=600;
            $show_form=false;

            $status_text=$lang==='tr'
                ?"E-posta değiştirme bağlantısı gönderildi."
                :"Email change link has been sent.";
        }
    }
}

if(isset($_SESSION['email_change_user_id'])){
    $stmt=$db->prepare("SELECT email_change_expires FROM users WHERE id=?");
    $stmt->execute([$_SESSION['email_change_user_id']]);
    $u=$stmt->fetch(PDO::FETCH_ASSOC);

    if($u && $u['email_change_expires']){
        $expires_at=strtotime($u['email_change_expires']);
        $remaining=$expires_at-time();

        if($remaining>0){
            $show_form=false;

            if(!$status_text){
                $status_text=$lang==='tr'
                    ?"E-posta değiştirme bağlantısı gönderildi."
                    :"Email change link has been sent.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<head>
<meta charset="UTF-8">
<title><?php echo $lang==='tr'?'E-Posta Değiştir':'Change Email'; ?></title>
<link rel="stylesheet" href="../styles.css">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
</head>
<body>

<div class="container">

<h2 style="text-align:center;">
<?php echo $lang==='tr'?'E-Posta Değiştir':'Change Email'; ?>
</h2>

<?php if($show_form): ?>

<form method="post">
<label><?php echo $lang==='tr'?'Kullanıcı Adı:':'Username:'; ?></label><br>
<input type="text" name="username" required><br>

<label><?php echo $lang==='tr'?'Parola:':'Password:'; ?></label><br>
<div style="position:relative;">
    <input type="password" id="password" name="password" oninput="checkMaxLength(this);" required>
    <span id="togglePassword" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); color:#888;"><i class="fa-solid fa-eye"></i></span>
</div>
            
<label><?php echo $lang==='tr'?'Yeni E-Posta:':'New Email:'; ?></label><br>
<input type="text" name="pending_email" required><br>

<?php if($error_text) echo "<p style='color:red;text-align:center;'>$error_text</p>"; ?>

<input type="submit" value="<?php echo $lang==='tr'?'Devam Et':'Continue'; ?>">
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

function formatTime(sec){
    const m=Math.floor(sec/60);
    const s=sec%60;
    return lang==="tr"?m+"dk "+s+"sn":m+"min "+s+"sec";
}

function startCountdown(){
    if(expiresAt<=0)return;

    countdownTimer=setInterval(()=>{
        const diff=Math.floor((expiresAt-Date.now())/1000);
        remaining=diff>0?diff:0;

        if(remaining>0){
            timeEl.textContent=(lang==="tr"?"Kalan süre: ":"Remaining time: ")+formatTime(remaining);
        }else{
            clearInterval(countdownTimer);
            timeEl.textContent="";
            statusEl.innerHTML=lang==="tr"
                ?"E-posta değiştirme süreniz sona erdi.<br>Ana sayfaya yönlendiriliyorsunuz."
                :"Your email change period has expired.<br>Redirecting to index.";
            setTimeout(()=>{
                window.location.href="/logout";
            },2000);
        }
    },1000);
}

function checkEmail(){
    fetch("check_email")
        .then(r=>r.text())
        .then(res=>{
            if(res.trim()==="1"){
                if(countdownTimer)clearInterval(countdownTimer);
                if(checkTimer)clearInterval(checkTimer);
                timeEl.textContent="";
                statusEl.innerHTML=lang==="tr"
                    ?"E-posta başarıyla değiştirildi.<br>Ana sayfaya yönlendiriliyorsunuz."
                    :"Email successfully changed.<br>Redirecting to index.";
                    
                setTimeout(()=>{
                    window.location.href="/logout";
                },2000);
            }
        });
}

if(remaining>0){
    startCountdown();
    checkTimer=setInterval(checkEmail,2000);
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
