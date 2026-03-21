<?php
session_start();
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $lang==='tr'?'Kullanım Şartları':'Terms of Use'; ?></title>
<link rel="shortcut icon" href="https://www.uzay.info/uzaylogo.ico">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
<style>
body{font-family:'Roboto',sans-serif;margin:0;padding:0}
.wrap{max-width:900px;margin:40px auto;background:rgba(102,0,255,0.8);border-radius:16px;box-shadow:0 8px 30px rgba(0,0,0,0.15);padding:22px 22px}
h1{margin:0 0 10px 0;color:#000e44;font-size:28px}
h2{margin:22px 0 10px 0;color:#000e44;font-size:18px}
p,li{color:#eef;line-height:1.6;font-size:14px}
ul{margin:8px 0 0 18px}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;gap:10px;flex-wrap:wrap}
a{color:#cfe0ff;text-decoration:underline}
.blue{color:royalblue}
.small{opacity:.9;font-size:12px}
.card{background:rgba(0,0,0,0.15);border-radius:12px;padding:14px 14px;margin-top:14px}
.btns{display:flex;gap:10px;flex-wrap:wrap}
.btn{display:inline-block;background:#072768;color:#fff;text-decoration:none;padding:8px 12px;border-radius:10px;font-size:13px}
.btn:hover{background:#0a3ca8}
hr{border:0;border-top:1px solid rgba(255,255,255,0.25);margin:18px 0}
</style>
</head>
<body>
<div class="wrap">
<div class="topbar">
<h1><?php echo $lang==='tr'?'Kullanım Şartları':'Terms of Use'; ?></h1>
<div class="btns">
<a class="btn" href="/"><?php echo $lang==='tr'?'Ana Sayfa':'Home'; ?></a>
<a class="btn" href="/privacy"><?php echo $lang==='tr'?'Gizlilik':'Privacy'; ?></a>
</div>
</div>

<div class="card">
<?php if($lang==='tr'): ?>
<p>Bu Kullanım Şartları, SpacePedia/Uzay platformunu kullanırken uymanız gereken kuralları açıklar. Platformu kullanarak bu şartları kabul etmiş olursunuz.</p>
<?php else: ?>
<p>These Terms of Use describe the rules you must follow when using SpacePedia/Uzay. By using the platform, you agree to these terms.</p>
<?php endif; ?>
</div>

<h2><?php echo $lang==='tr'?'1. Hizmetin Kapsamı':'1. Service Scope'; ?></h2>
<?php if($lang==='tr'): ?>
<ul>
<li>Platform; içerik paylaşımı, profil oluşturma ve ilgili özellikleri sunar.</li>
<li>Hizmetler “olduğu gibi” sunulur, kesintisiz veya hatasız olacağı garanti edilmez.</li>
</ul>
<?php else: ?>
<ul>
<li>The platform provides content sharing, profiles, and related features.</li>
<li>Services are provided “as is” and may be interrupted or contain errors.</li>
</ul>
<?php endif; ?>

<h2><?php echo $lang==='tr'?'2. Hesap ve Güvenlik':'2. Account & Security'; ?></h2>
<?php if($lang==='tr'): ?>
<ul>
<li>Hesap bilgilerinizin gizliliğinden siz sorumlusunuz.</li>
<li>Şüpheli kullanım tespit ederseniz bizimle iletişime geçiniz.</li>
<li>Kötüye kullanım önleme amacıyla bazı güvenlik kontrolleri uygulanabilir.</li>
</ul>
<?php else: ?>
<ul>
<li>You are responsible for keeping your account credentials secure.</li>
<li>Contact us if you suspect unauthorized use.</li>
<li>We may apply security checks to prevent abuse.</li>
</ul>
<?php endif; ?>

<h2><?php echo $lang==='tr'?'3. Kabul Edilemez Kullanım':'3. Unacceptable Use'; ?></h2>
<?php if($lang==='tr'): ?>
<ul>
<li>Spam, otomasyon/bot kullanımı, sistemleri aşırı yükleme veya güvenlik açıklarını kötüye kullanma yasaktır.</li>
<li>Başkasını taklit etmek, izinsiz erişim denemek veya platforma zarar vermek yasaktır.</li>
<li>Yasalara aykırı içerik paylaşmak yasaktır.</li>
</ul>
<?php else: ?>
<ul>
<li>Spam, automation/bots, overloading systems, or exploiting vulnerabilities is prohibited.</li>
<li>Impersonation, unauthorized access attempts, or harming the platform is prohibited.</li>
<li>Posting illegal content is prohibited.</li>
</ul>
<?php endif; ?>

<h2><?php echo $lang==='tr'?'4. İçerikler ve Sorumluluk':'4. Content & Responsibility'; ?></h2>
<?php if($lang==='tr'): ?>
<ul>
<li>Paylaştığınız içeriklerden siz sorumlusunuz.</li>
<li>Platform, kullanıcı içeriklerini önceden incelemek zorunda değildir; gerektiğinde kaldırabilir.</li>
</ul>
<?php else: ?>
<ul>
<li>You are responsible for the content you post.</li>
<li>We are not required to pre-review user content and may remove content when necessary.</li>
</ul>
<?php endif; ?>

<h2><?php echo $lang==='tr'?'5. Hesap Kapatma ve Kısıtlama':'5. Suspension & Termination'; ?></h2>
<?php if($lang==='tr'): ?>
<ul>
<li>Şartların ihlali durumunda hesabınız geçici veya kalıcı olarak kısıtlanabilir/kapatılabilir.</li>
<li>Güvenlik gerekçesiyle bazı işlemler sınırlandırılabilir.</li>
</ul>
<?php else: ?>
<ul>
<li>If you violate these terms, your account may be temporarily or permanently restricted/closed.</li>
<li>We may limit certain actions for security reasons.</li>
</ul>
<?php endif; ?>

<h2><?php echo $lang==='tr'?'6. Değişiklikler':'6. Changes'; ?></h2>
<?php if($lang==='tr'): ?>
<p>Bu şartlar zaman zaman güncellenebilir. Güncellenmiş şartlar bu sayfada yayınlandığı anda geçerli olur.</p>
<?php else: ?>
<p>We may update these terms from time to time. Updated terms take effect once published on this page.</p>
<?php endif; ?>

<hr>

<div class="small">
<?php if($lang==='tr'): ?>
<p>Gizlilik uygulamalarımız için <a class="blue" href="/privacy">Gizlilik Politikası</a> sayfasını inceleyiniz.</p>
<?php else: ?>
<p>For information about data practices, see our <a class="blue" href="/privacy">Privacy Policy</a>.</p>
<?php endif; ?>
</div>
</div>
</body>
</html>
