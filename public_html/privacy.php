<?php
session_start();
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $lang==='tr'?'Gizlilik Politikası':'Privacy Policy'; ?></title>
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
<h1><?php echo $lang==='tr'?'Gizlilik Politikası':'Privacy Policy'; ?></h1>
<div class="btns">
<a class="btn" href="/"><?php echo $lang==='tr'?'Ana Sayfa':'Home'; ?></a>
<a class="btn" href="/terms"><?php echo $lang==='tr'?'Şartlar':'Terms'; ?></a>
</div>
</div>

<div class="card">
<?php if($lang==='tr'): ?>
<p>Bu Gizlilik Politikası, SpacePedia/Uzay platformunda hangi verilerin işlendiğini, neden işlendiğini ve nasıl korunduğunu açıklar.</p>
<?php else: ?>
<p>This Privacy Policy explains what data we process on SpacePedia/Uzay, why we process it, and how we protect it.</p>
<?php endif; ?>
</div>

<h2><?php echo $lang==='tr'?'1. İşlediğimiz Veriler':'1. Data We Process'; ?></h2>
<?php if($lang==='tr'): ?>
<ul>
<li>Hesap bilgileri: kullanıcı adı, e-posta, şifre (hashlenmiş).</li>
<li>Profil bilgileri: profil metni ve profil görseli (eklediyseniz).</li>
<li>Güvenlik verileri: IP adresi gibi kayıt/giriş güvenliğine yönelik bilgiler (kötüye kullanım önleme).</li>
<li>Teknik veriler: oturum bilgileri ve temel hata/işlem kayıtları (güvenlik ve performans).</li>
</ul>
<?php else: ?>
<ul>
<li>Account data: username, email, password (hashed).</li>
<li>Profile data: profile text and profile image (if provided).</li>
<li>Security data: information such as IP address for registration/login security (abuse prevention).</li>
<li>Technical data: session details and basic logs (security and performance).</li>
</ul>
<?php endif; ?>

<h2><?php echo $lang==='tr'?'2. Verileri Neden İşliyoruz':'2. Why We Process Data'; ?></h2>
<?php if($lang==='tr'): ?>
<ul>
<li>Hesap oluşturma, giriş ve hesap yönetimi.</li>
<li>Platform güvenliği ve spam/bot/kötüye kullanım önleme.</li>
<li>E-posta doğrulama, parola sıfırlama ve hesap güvenliği süreçleri.</li>
<li>Hizmetin çalışması, geliştirilmesi ve sorun giderme.</li>
</ul>
<?php else: ?>
<ul>
<li>Account creation, login, and account management.</li>
<li>Platform security and abuse/spam/bot prevention.</li>
<li>Email verification, password reset, and account security processes.</li>
<li>Operating, improving, and troubleshooting the service.</li>
</ul>
<?php endif; ?>

<h2><?php echo $lang==='tr'?'3. Çerezler ve Oturum':'3. Cookies & Sessions'; ?></h2>
<?php if($lang==='tr'): ?>
<p>Platform, oturumunuzu sürdürebilmek için teknik olarak gerekli oturum mekanizmalarını kullanabilir. Belli bir amaçla takip çerezleri kullanılıyorsa ayrıca bilgilendirme yapılır.</p>
<?php else: ?>
<p>The platform may use technically necessary session mechanisms to keep you signed in. You will also be notified if tracking cookies are used for a specific purpose.</p>
<?php endif; ?>

<h2><?php echo $lang==='tr'?'4. Veri Paylaşımı':'4. Data Sharing'; ?></h2>
<?php if($lang==='tr'): ?>
<p>Verilerinizi satmayız. Yalnızca yasal zorunluluklar veya güvenlik gereklilikleri kapsamında paylaşım yapılabilir.</p>
<?php else: ?>
<p>We do not sell your data. We may share data only when legally required or necessary for security purposes.</p>
<?php endif; ?>

<h2><?php echo $lang==='tr'?'5. Veri Güvenliği':'5. Data Security'; ?></h2>
<?php if($lang==='tr'): ?>
<ul>
<li>Şifreler düz metin tutulmaz; güvenli şekilde hashlenir.</li>
<li>Yetkisiz erişimi önlemek için teknik ve organizasyonel önlemler uygulanır.</li>
</ul>
<?php else: ?>
<ul>
<li>Passwords are not stored in plain text; they are securely hashed.</li>
<li>We use technical and organizational measures to prevent unauthorized access.</li>
</ul>
<?php endif; ?>

<h2><?php echo $lang==='tr'?'6. Saklama Süresi':'6. Retention'; ?></h2>
<?php if($lang==='tr'): ?>
<p>Veriler, hizmeti sunmak ve güvenlik/uyumluluk gereklerini karşılamak için gerekli olduğu süre boyunca saklanır.</p>
<?php else: ?>
<p>We retain data for as long as needed to provide the service and meet security/compliance requirements.</p>
<?php endif; ?>

<h2><?php echo $lang==='tr'?'7. Haklarınız':'7. Your Rights'; ?></h2>
<?php if($lang==='tr'): ?>
<p>Yürürlükteki mevzuata göre verilerinizle ilgili belirli haklara sahip olabilirsiniz. Talepleriniz için bizimle iletişime geçebilirsiniz.</p>
<?php else: ?>
<p>Depending on applicable law, you may have certain rights regarding your data. You can contact us with requests.</p>
<?php endif; ?>

<hr>

<div class="small">
<?php if($lang==='tr'): ?>
<p>Kullanım koşulları için <a class="blue" href="/terms">Kullanım Şartları</a> sayfasını inceleyiniz.</p>
<?php else: ?>
<p>For usage rules, see our <a class="blue" href="/terms">Terms of Service</a>.</p>
<?php endif; ?>
</div>
</div>
</body>
</html>
