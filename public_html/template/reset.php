<?php
require_once '../db_config.php';
require_once __DIR__.'/mailer.php';

$lang = $_GET['lang'] ?? 'tr';
$token = $_GET['token'] ?? null;

$status = '';
$show_form = false;

$reset_link = '../reset_password';

if (!$token) {
    $status = $lang === 'tr'
        ? "Geçersiz parola yenileme bağlantısı. Yeni bir bağlantı için <a href='$reset_link' style='color:blue;'>tıklayınız</a>"
        : "Invalid password reset link.  Click <a href='$reset_link' style='color:blue;'>here</a> to request a new one.";
} else {
    $stmt = $db->prepare("SELECT id, reset_token_expires FROM users WHERE reset_token=?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (strtotime($user['reset_token_expires']) > time()) {
            $show_form = true;
        } else {
            $db->prepare("UPDATE users SET reset_token=NULL, reset_token_expires=NULL WHERE id=?")
               ->execute([$user['id']]);

            $status = $lang === 'tr'
                ? "Parola yenileme bağlantısının süresi doldu. Yeni bir bağlantı için <a href='$reset_link' style='color:blue;'>tıklayınız</a>."
                : "The password reset link has expired. Click <a href='$reset_link' style='color:blue;'>here</a> to request a new one.";
        }
    } else {
        $status = $lang === 'tr'
            ? "Geçersiz parola yenileme bağlantısı. Yeni bir bağlantı için <a href='$reset_link' style='color:blue;'>tıklayınız</a>"
            : "Invalid password reset link.  Click <a href='$reset_link' style='color:blue;'>here</a> to request a new one.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'], $_POST['token'])) {
    $stmt = $db->prepare("
        UPDATE users 
        SET password=?, reset_token=NULL, reset_token_expires=NULL 
        WHERE reset_token=? AND reset_token_expires > NOW()
    ");
    $stmt->execute([
        password_hash($_POST['password'], PASSWORD_DEFAULT),
        $_POST['token']
    ]);

    if ($stmt->rowCount()) {
        $stmtUser = $db->prepare("SELECT email,username FROM users WHERE reset_token IS NULL ORDER BY id DESC LIMIT 1");
        $stmtUser->execute();
        $userMail = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if ($userMail) {
            sendPasswordChangedSuccessMail($userMail['email'],$userMail['username'],$lang);
        }

        $status = $lang === 'tr'
            ? "Parolanız başarıyla güncellendi."
            : "Your password has been updated.";
    } else {
        $status = $lang === 'tr'
            ? "Parola yenileme bağlantısının süresi doldu. Yeni bir bağlantı için <a href='$reset_link' style='color:blue;'>tıklayınız</a>."
            : "The password reset link has expired. Click <a href='$reset_link' style='color:blue;'>here</a> to request a new one.";
    }

    $show_form = false;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<head>
<meta charset="UTF-8">
<title><?php echo $lang === 'tr' ? 'Parola Yenileme' : 'New Password'; ?></title>
<link rel="stylesheet" type="text/css" href="../styles.css">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
</head>
<body>

<div class="container" style="text-align:center;">
<h2><?php echo $lang === 'tr' ? "Parola Yenileme" : "New Password"; ?></h2>
<?php if ($show_form): ?>
<form method="post">
<input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
<label for="password"><?php echo $lang === 'tr' ? "Yeni Parola:" : "New Password:"; ?></label><br>
<div id="passwordBarContainer">
    <div class="seg"></div>
    <div class="seg"></div>
    <div class="seg"></div>
    <div class="seg"></div>
</div>
<div style="position:relative;">
    <input type="password" id="password" name="password" oninput="checkPasswordStrength(this);" required>
    <span id="togglePassword" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); color:#888;"><i class="fa-solid fa-eye"></i></span>
</div>
<div id="passwordNote"></div>
<input type="submit" value="<?php echo $lang === 'tr' ? 'Kaydet' : 'Save'; ?>">
</form>
<?php else: ?>
<p style="color:white;"><?php echo $status; ?></p>
<?php endif; ?>
</div>

<script>
function checkPasswordStrength(input){
    const v=input.value;
    const note=document.getElementById("passwordNote");
    const segs=document.querySelectorAll("#passwordBarContainer .seg");
    segs.forEach(s=>{s.style.background="#333";});
    if(!v){
        note.textContent="";
        input.setCustomValidity('');
        return;
    }
    const r=zxcvbn(v);
    const score=r.score;
    let color="";
    if(score===1) color="#e74c3c";
    else if(score===2) color="#f39c12";
    else if(score===3) color="#f1c40f";
    else if(score===4) color="#27ae60";
    for(let i=0;i<score;i++){
        if(segs[i]) segs[i].style.background=color;
    }
    note.textContent=
        score===1 ? "<?php echo $lang==='tr'?'Parola çok zayıf':'Password is very weak'; ?>" :
        score===2 ? "<?php echo $lang==='tr'?'Parola zayıf':'Password is weak'; ?>" :
        score===3 ? "<?php echo $lang==='tr'?'Parola orta':'Password is medium'; ?>" :
        score===4 ? "<?php echo $lang==='tr'?'Parola güçlü':'Password is strong'; ?>" :
        "";
    if(score<3) input.setCustomValidity("<?php echo $lang==='tr'?'Parola en az orta seviye olmalıdır.':'Password must be at least medium.'; ?>");
    else if(input.value.length>100) input.setCustomValidity("<?php echo $lang==='tr'?'Parola en fazla 100 karakter olabilir.':'Password can be max 100 characters.'; ?>");
    else input.setCustomValidity('');
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
