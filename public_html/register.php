<?php
session_start();
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
require_once('db_config.php');
require_once('template/mailer.php');

function getUserIP(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',',$_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    return $_SERVER['REMOTE_ADDR'] ?? null;
}

function isValidUsernameServer($value){
    $value=trim((string)$value);
    if($value===''||strlen($value)<3||strlen($value)>20) return false;
    if(!preg_match('/^(?![_-])[A-Za-z_-]+(?<![_-])\d*$/',$value)) return false;
    if(preg_match('/[AEIOUaeiou]{3}/',$value)) return false;
    if(preg_match('/[BCDFGHJKLMNPQRSTVWXYZbcdfghjklmnpqrstvwxyz]{4}/',$value)) return false;
    if(preg_match('/([A-Za-z])\1\1|_{3,}|-{3,}/',$value)) return false;
    if(preg_match('/[_-].*[_-].*[_-]/',$value)) return false;
    return true;
}

if($_SERVER["REQUEST_METHOD"]==="POST"){
    if(!empty($_POST['website'])){exit;}
    $username=isset($_POST['username'])?trim($_POST['username']):'';
    $email=isset($_POST['email'])?trim($_POST['email']):'';
    $password=isset($_POST['password'])?$_POST['password']:'';
    $register_ip=getUserIP();

    if(empty($_SESSION['captcha_ok'])){
        $msg=$lang==='tr'?"Lütfen Ben robot değilim doğrulamasını yapınız.":"Please complete the I am not a robot verification.";
    }elseif($username===''||$email===''||$password===''){
        $msg=$lang==='tr'?"Lütfen tüm alanları doldurunuz.":"Please fill in all fields.";
    }elseif($register_ip===null){
        $msg=$lang==='tr'?"Kayıt sırasında beklenmeyen bir hata oluştu.":"An unexpected error occurred during registration. Please try again later.";
    }elseif(!isValidUsernameServer($username)){
        $msg=$lang==='tr'
            ?"Sadece İngilizce harfler (A-Z), tire (-), alt çizgi (_) ve rakam kullanılabilir. Rakamlar sonda olabilir. 3 sesli veya 4 sessiz harf yan yana gelemez. Aynı harf 3 kez, veya 3+ tire/alt çizgi kullanılamaz."
            :"Only English letters (A-Z), hyphens (-), underscores (_), and numbers are allowed. Numbers may appear only at the end. 3 vowels or 4 consonants cannot appear consecutively. Also, no 3x repeated letter, no 3+ hyphens/underscores, separators.";
    }else{
        unset($_SESSION['captcha_ok']);

        $stmt=$db->prepare("SELECT COUNT(*) FROM users WHERE username=:username OR email=:email");
        $stmt->bindParam(':username',$username);
        $stmt->bindParam(':email',$email);
        $stmt->execute();
        if((int)$stmt->fetchColumn()>0){
            $msg=$lang==='tr'?"Bu kullanıcı adı veya e-posta zaten kayıtlı.":"This username or email is already registered.";
        }else{
            $activeStmt=$db->prepare("SELECT COUNT(*) FROM users WHERE register_ip=:ip AND account_closed=0");
            $activeStmt->bindParam(':ip',$register_ip);
            $activeStmt->execute();
            $activeCount=(int)$activeStmt->fetchColumn();

            $closedStmt=$db->prepare("SELECT COUNT(*) FROM users WHERE register_ip=:ip AND account_closed=1");
            $closedStmt->bindParam(':ip',$register_ip);
            $closedStmt->execute();
            $closedCount=(int)$closedStmt->fetchColumn();

            $dailyStmt=$db->prepare("SELECT COUNT(*) FROM users WHERE register_ip=:ip AND created_at >= NOW() - INTERVAL 1 DAY");
            $dailyStmt->bindParam(':ip',$register_ip);
            $dailyStmt->execute();
            $dailyCount=(int)$dailyStmt->fetchColumn();

            if($activeCount>=3||$closedCount>=5||$dailyCount>=2){
                $msg=$lang==='tr'
                    ?"Bu cihazdan çok fazla hesap açılmıştır. Lütfen daha sonra tekrar deneyiniz."
                    :"Too many accounts have been created from this device. Please try again later.";
            }else{
                try{
                    $hashed=password_hash($password,PASSWORD_DEFAULT);
                    $token=bin2hex(random_bytes(32));
                    $expires=date('Y-m-d H:i:s',time()+300);

                    $stmt=$db->prepare("INSERT INTO users (username,email,password,email_token,email_token_expires,last_activation_sent,register_ip) VALUES (:username,:email,:password,:token,:expires,NOW(),:register_ip)");
                    $stmt->bindParam(':username',$username);
                    $stmt->bindParam(':email',$email);
                    $stmt->bindParam(':password',$hashed);
                    $stmt->bindParam(':token',$token);
                    $stmt->bindParam(':expires',$expires);
                    $stmt->bindParam(':register_ip',$register_ip);
                    $stmt->execute();

                    sendActivationMail($email,$username,$token,$lang);
                    $_SESSION['resend_login']=$username;
                    header("Location: /resend_activation");
                    exit;
                }catch(PDOException $e){
                    $msg=$lang==='tr'?"Kayıt sırasında hata oluştu.":"An error occurred during registration.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $lang === 'tr' ? "Kayıt Ol" : "Sign Up"; ?></title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
    <style>
        .recaptcha{width:280px;border:1px solid #d3d3d3;border-radius:4px;padding:12px;background:#f9f9f9}
        .top{display:flex;justify-content:space-between;align-items:center}
        .left2{display:flex;align-items:center;gap:10px;cursor:pointer}
        .checkbox{width:24px;height:24px;border:2px solid #c1c1c1;border-radius:2px;display:flex;align-items:center;justify-content:center;background:#fff;transition:.2s}
        .checkbox.ok{border-color:#1a73e8;background:#fff}
        .checkbox.ok::after{content:"";width:22px;height:22px;background:no-repeat center/contain url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCI+PHBhdGggZD0iTTQgMTMuNWw1IDVMIDIwIDYuNSIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjMWE3M2U4IiBzdHJva2Utd2lkdGg9IjMiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjwvc3ZnPg==")}
        .spinner{width:18px;height:18px;border:3px solid #ccc;border-top:3px solid #1a73e8;border-radius:50%;animation:spin 1s linear infinite;display:none}
        @keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}
        .challenge{display:none;margin-top:10px}
        canvas{background:#eee;border-radius:4px}
        .blue{color:royalblue}
    </style>
</head>
<body>
    <div class="container">
        <h2><?php echo $lang === 'tr' ? "Kayıt Ol" : "Sign Up"; ?></h2>
        <form method="post" action="" onsubmit="return checkCaptcha();">
            <input type="text" name="website" style="display:none" autocomplete="off">
            <label for="username"><?php echo $lang === 'tr' ? "Kullanıcı Adı:" : "Username:"; ?></label><br>
            <input type="text" id="username" name="username" maxlength="20" oninput="validateUsername(this)" required><br>
            <div style="color:green;font-size:10px;line-height:1.2;text-align:left;">
                <?php echo $lang==='tr'?'Kullanıcı Adınızı daha sonra değiştiremezsiniz dolayısıyla uygun bir Kullanıcı Adı belirlediğinizden emin olunuz.':'You will not be able to change your Username later, so please make sure you choose an appropriate one.'; ?>
            </div><br>
            <label for="email"><?php echo $lang === 'tr' ? "E-Posta:" : "E-Mail:"; ?></label><br>
            <input type="text" id="email" name="email" minlength="6" maxlength="100" pattern="^[^@]+@[^@]+\.[^@]+$" required><br>
            <label for="password"><?php echo $lang === 'tr' ? "Parola:" : "Password:"; ?></label><br>
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
            <?php if(!empty($msg)){echo "<p style='color:red; text-align:center;'>$msg</p>";}?>
            <div style="color:green;font-size:12px;text-align:left;margin-bottom:6px;">
            <?php
            echo $lang==='tr'
            ?'Not: Kayıt işlemini tamamladıktan sonra aktivasyon e-postası gönderilecektir. Lütfen e-posta onayı gereken her işlemde her zaman tüm gereksiz (junk), spam ve diğer klasörleri kontrol ediniz.'
            :'Note: After completing the registration, an activation email will be sent. Please always check all junk, spam, and other folders for any email verification required.';
            ?>
            </div>
            <br>
            <div class="recaptcha">
                <div class="top" onclick="startCaptcha()">
                    <div class="left2">
                        <div class="checkbox" id="cb"></div>
                        <div class="spinner" id="sp"></div>
                        <span><?php echo $lang === 'tr' ? "Ben robot değilim." : "I am not a robot."; ?></span>
                    </div>
                    <img src="/static/recaptcha.png" width="28">
                </div>
            </div>
                <div class="challenge" id="ch">
                    <canvas id="cv" width="180" height="60"></canvas><br>
                    <input type="text" id="inp" placeholder="<?php echo $lang === 'tr' ? 'Gördüğünüzü yazınız.' : 'Write what you see.'; ?>">
                </div>
            <br>
            <div style="font-size:11px;color:#aaa;text-align:left;">
                <?php echo $lang==='tr'
                ?'Kayıt olarak <a href="/terms" class="blue">Kullanım Şartları</a>\'nı kabul etmiş olursunuz. <a href="/privacy" class="blue">Gizlilik Politikamızı</a> okuyunuz.'
                :'By registering, you agree to the <a href="/terms" class="blue">Terms of Use</a>. Read about our <a href="/privacy" class="blue">Privacy Policy</a>.'; ?>
            </div>
            <br>
            <input type="submit" value="<?php echo $lang === 'tr' ? "Kayıt Ol" : "Sign Up"; ?>">
        </form>
        <a href="/login" class="blue"><?php echo $lang === 'tr' ? "Giriş Yap" : "Login"; ?></a>
    </div>
<script>
    function validateUsername(input){const value=input.value;const pattern=/^(?![_-])[A-Za-z_-]+(?<![_-])\d*$/;const vowel=/[AEIOUaeiou]{3}/;const cons=/[BCDFGHJKLMNPQRSTVWXYZbcdfghjklmnpqrstvwxyz]{4}/;const repeat=/([A-Za-z])\1\1|_{3,}|-{3,}/;const tooManySep=/[_-].*[_-].*[_-]/;if(value.length<3||value.length>20||!pattern.test(value)||vowel.test(value)||cons.test(value)||repeat.test(value)||tooManySep.test(value)){input.setCustomValidity("<?php echo $lang==='tr'?'Sadece İngilizce harfler (A-Z), tire (-), alt çizgi (_) ve rakam kullanılabilir. Rakamlar sonda olabilir. 3 sesli veya 4 sessiz harf yan yana gelemez. Aynı harf 3 kez, veya 3+ tire/alt çizgi kullanılamaz.':'Only English letters (A-Z), hyphens (-), underscores (_), and numbers are allowed. Numbers may appear only at the end. 3 vowels or 4 consonants cannot appear consecutively. Also, no 3x repeated letter, no 3+ hyphens/underscores, separators.'; ?>");}else{input.setCustomValidity('');}}
    function checkPasswordStrength(input){const v=input.value;const note=document.getElementById("passwordNote");const segs=document.querySelectorAll("#passwordBarContainer .seg");segs.forEach(s=>{s.style.background="#333";});if(!v){note.textContent="";input.setCustomValidity('');return;}const r=zxcvbn(v);const score=r.score;let color="";if(score===1)color="#e74c3c";else if(score===2)color="#f39c12";else if(score===3)color="#f1c40f";else if(score===4)color="#27ae60";for(let i=0;i<score;i++){if(segs[i])segs[i].style.background=color;}note.textContent=score===1?"<?php echo $lang==='tr'?'Parola çok zayıf':'Password is very weak'; ?>":score===2?"<?php echo $lang==='tr'?'Parola zayıf':'Password is weak'; ?>":score===3?"<?php echo $lang==='tr'?'Parola orta':'Password is medium'; ?>":score===4?"<?php echo $lang==='tr'?'Parola güçlü':'Password is strong'; ?>":"";if(score<3)input.setCustomValidity("<?php echo $lang==='tr'?'Parola en az orta seviye olmalıdır.':'Password must be at least medium.'; ?>");else if(input.value.length>100)input.setCustomValidity("<?php echo $lang==='tr'?'Parola en fazla 100 karakter olabilir.':'Password can be max 100 characters.'; ?>");else input.setCustomValidity('');}
    const togglePassword=document.getElementById("togglePassword");const passwordInput=document.getElementById("password");togglePassword.addEventListener("click",()=>{const icon=togglePassword.querySelector("i");if(passwordInput.type==="password"){passwordInput.type="text";icon.className="fa-solid fa-eye-slash";}else{passwordInput.type="password";icon.className="fa-solid fa-eye";}});
    let val="";let t=0;let alpha=1;let started=false;let timer=null;
    const cv=document.getElementById("cv");const ctx=cv.getContext("2d");const cb=document.getElementById("cb");const sp=document.getElementById("sp");
    const off=document.createElement("canvas");off.width=180;off.height=60;const offctx=off.getContext("2d");
    let chars=[];
    function gen(){val='';let arr=[];for(let i=0;i<3;i++){arr.push(String.fromCharCode(65+Math.floor(Math.random()*26)));}for(let i=0;i<2;i++){arr.push(Math.floor(Math.random()*10));}val=arr.sort(()=>Math.random()-0.5).join('');fetch("captcha_set.php",{method:"POST",body:new URLSearchParams({v:val})});alpha=1;t=0;chars=[];for(let i=0;i<val.length;i++){chars.push({x:20+i*28,y:42,sx:Math.random()*10,sy:Math.random()*10,w:Math.random()*6+4});}document.getElementById("inp").value="";}
    function draw(){offctx.clearRect(0,0,180,60);offctx.font="34px Arial";offctx.fillStyle="#333";for(let i=0;i<val.length;i++){let c=val[i];let o=chars[i];let dx=Math.sin(t/o.sx)*o.w;let dy=Math.cos(t/o.sy)*o.w;offctx.fillText(c,o.x+dx,o.y+dy);}ctx.clearRect(0,0,180,60);ctx.globalAlpha=alpha;for(let y=0;y<60;y++){let warp=Math.sin((y+t)/6)*5;ctx.drawImage(off,0,y,180,1,warp,y,180,1);}for(let i=0;i<30;i++){ctx.fillRect(Math.random()*180,Math.random()*60,2,2);}}
    function startCaptcha(){if(started)return;started=true;sp.style.display="block";cb.style.display="none";document.getElementById("ch").style.display="block";gen();timer=setInterval(()=>{t++;alpha-=0.001;if(alpha<=0){gen();}draw();},60);}
    let captchaOk=false;
    document.getElementById("inp").addEventListener("input",e=>{if(e.target.value.length!==val.length)return;fetch("captcha_check.php",{method:"POST",body:new URLSearchParams({v:e.target.value})}).then(r=>r.text()).then(res=>{if(res==="OK"){captchaOk=true;clearInterval(timer);sp.style.display="none";cb.style.display="flex";cb.classList.add("ok");document.getElementById("ch").style.display="none";}});});
    function checkCaptcha(){if(!captchaOk){alert("<?php echo $lang==='tr'?'Lütfen Ben robot değilim doğrulamasını yapınız.':'Please complete the I am not a robot verification.'; ?>");return false;}return true;}
</script>
</body>
</html>
