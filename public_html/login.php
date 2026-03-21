<?php
session_start();
if(isset($_GET['lang'])){ $lang=$_GET['lang']; $_SESSION['lang']=$lang; }
elseif(isset($_SESSION['lang'])){ $lang=$_SESSION['lang']; }
else{ $lang='tr'; }

require_once 'db_config.php';

function getUserIP(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    return $_SERVER['REMOTE_ADDR'] ?? null;
}

$msg='';
$show_resend=false;
$login_value='';
$user=null;

if($_SERVER["REQUEST_METHOD"]==="POST"){
    $login=$_POST['login'];
    $password=$_POST['password'];
    $login_value=htmlspecialchars($login);
    $login_ip=getUserIP();

    $stmt=$db->prepare("
        SELECT id,username,email,password,
               email_verified,
               account_closed
        FROM users
        WHERE username=:login OR email=:login
    ");
    $stmt->bindParam(':login',$login);
    $stmt->execute();
    $user=$stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password,$user['password'])){

        if((int)$user['account_closed']===1){
            $msg=$lang==='tr'
                ? "Bu hesap kapatılmıştır. Yeniden açmak için bağlantıyı kullanabilirsiniz."
                : "This account is closed. You can use the reopen link.";
            $_SESSION['resend_login']=$login;
            $show_resend=true;
        }
        elseif((int)$user['account_closed']===-1){
            $msg=$lang==='tr'
                ? "Bu Hesap SpacePedia/Uzay Platformunun Kullanım Şartlarını İhlâl Ettiği için Kapatılmıştır."
                : "This Account Has Been closed Due to Violation of SpacePedia/Uzay Platform Terms of Use.";
        }
        elseif((int)$user['email_verified']===0){
            $msg=$lang==='tr'
                ? "Hesabınız aktifleştirilmemiş. Lütfen e-postanızı kontrol edin."
                : "Your account is not activated. Please check your email.";
            $_SESSION['resend_login']=$login;
            $show_resend=true;
        }
        else{
            $upd=$db->prepare("
                UPDATE users SET
                    last_login_ip=:ip,
                    last_online=NOW()
                WHERE id=:id
            ");
            $upd->bindParam(':ip',$login_ip);
            $upd->bindParam(':id',$user['id']);
            $upd->execute();

            $_SESSION['user_id']=$user['id'];
            $_SESSION['username']=$user['username'];
            $_SESSION['email']=$user['email'];
            unset($_SESSION['resend_login']);

            header("Location: index.php");
            exit;
        }

    }else{
        $msg=$lang==='tr'
            ? "Kullanıcı adı, e-posta veya parola hatalı."
            : "Username, email, or password is incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $lang==='tr'?'Giriş Yap':'Login'; ?></title>

<link rel="stylesheet" type="text/css" href="styles.css">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="container">
<h2><?php echo $lang==='tr'?'Giriş Yap':'Login'; ?></h2>

<form method="post">

<label><?php echo $lang==='tr'?'Kullanıcı Adı veya E-Posta:':'Username or E-Mail:'; ?></label><br>
<input type="text" name="login" minlength="4" maxlength="100" required value="<?php echo $login_value; ?>"><br>

<label><?php echo $lang==='tr'?'Parola:':'Password:'; ?></label><br>
<div style="position:relative;">
    <input type="password" id="password" name="password" oninput="checkMaxLength(this);" required>
    <span id="togglePassword" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);color:#888;">
        <i class="fa-solid fa-eye"></i>
    </span>
</div>

<?php if($msg) echo "<p style='color:red;text-align:center;'>$msg</p>"; ?>

<input type="submit" value="<?php echo $lang==='tr'?'Giriş Yap':'Login'; ?>">
</form>

<div class="left">
<a href="/change_email" style="color:green;margin-top:10px;">
<?php echo $lang==='tr'?'E-Posta Değiştir':'Change Email'; ?>
</a>

<a href="/reset_password" style="color:green;margin-top:10px;">
<?php echo $lang==='tr'?'Parola Yenileme':'Password Reset'; ?>
</a>
</div>

<div class="right">
<?php if($show_resend): ?>

    <?php if($user && (int)$user['account_closed']===1): ?>
        <a href="/reopen_account" style="color:red;margin-top:10px;">
        <?php echo $lang==='tr'?'Hesabı Yeniden Aç':'Reopen Account'; ?>
        </a>
    <?php else: ?>
        <a href="/resend_activation" style="color:red;margin-top:10px;">
        <?php echo $lang==='tr'?'Hesap Aktivasyonu':'Account Activation'; ?>
        </a>
    <?php endif; ?>

    <a href="/register" style="color:royalblue;margin-top:10px;">
    <?php echo $lang==='tr'?'Kayıt Ol':'Sign Up'; ?>
    </a>

<?php else: ?>

    <a href="/register" style="color:royalblue;margin-top:23px;">
    <?php echo $lang==='tr'?'Kayıt Ol':'Sign Up'; ?>
    </a>

<?php endif; ?>
</div>

</div>

<script>
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
</script>

</body>
</html>
