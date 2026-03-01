<?php
session_start();
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
?>
<!DOCTYPE HTML>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $lang=='tr'?'İletişim Sayfası':'Contact Page'; ?></title>

<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">

<style>
html,body{margin:0;padding:0;width:100%;height:100%;overflow:auto;}
body{display:flex;justify-content:center;align-items:flex-start;}
.container{max-width:760px;width:100%;background:#1e1e1e;color:#fff;padding:32px;border-radius:18px;box-shadow:0 0 30px rgba(255,255,255,.25);transition:.35s;margin:40px 15px}
.container:hover{box-shadow:0 0 50px rgba(0,170,255,.6),0 0 100px rgba(0,170,255,.3)}
h1{text-align:center;margin-bottom:20px}
p{font-size:15px;line-height:1.7}
.section{margin-top:25px;padding:18px;background:#111;border-radius:14px}
.section-title{font-weight:700;color:#00aaff;margin-bottom:8px}
.mail{margin-top:30px;padding:18px;background:#072768;border-radius:14px;text-align:center;transition:.35s}
.mail:hover{background:#0a3a9a;box-shadow:0 0 22px rgba(0,170,255,.9),0 0 45px rgba(0,170,255,.6)}
.mail a{color:#fff;font-size:16px;font-weight:700;text-decoration:none}
.mail a:hover{text-decoration:underline}
.note{margin-top:18px;font-size:14px;opacity:.9;text-align:center}
</style>
</head>
<body>

<div class="container">
<h1><?php echo $lang=='tr'?'İletişim Sayfası':'Contact Page'; ?></h1>

<p>
<?php echo $lang=='tr'
?'Aşağıdaki konularla ilgili bizimle iletişime geçmek için e-posta gönderebilirsiniz:'
:'You can contact us via email regarding the following topics:'; ?>
</p>

<div class="section">
<div class="section-title"><?php echo $lang=='tr'?'Genel İletişim':'General Contact'; ?></div>
<p>
<?php echo $lang=='tr'
?'Her türlü farklı talebiniz için bizimle e-posta yoluyla iletişime geçebilirsiniz.'
:'You can contact us via email for any kind of different requests.'; ?>
</p>
</div>

<div class="section">
<div class="section-title"><?php echo $lang=='tr'?'Websitesi Kullanımı':'Website Usage'; ?></div>
<p>
<?php echo $lang=='tr'
?'Site kullanımıyla ilgili yaşadığınız sorunlar, anlamadığınız bölümler veya kullanım hakkında genel sorularınız için bizimle iletişime geçebilirsiniz.'
:'You can contact us for issues you encounter while using the website, sections you do not understand, or general questions about usage.'; ?>
</p>
</div>

<div class="section">
<div class="section-title"><?php echo $lang=='tr'?'Öneri / Geri Bildirim':'Suggestions / Feedback'; ?></div>
<p>
<?php echo $lang=='tr'
?'Siteyle ilgili görüşlerinizi, geliştirme fikirlerinizi ve genel geri bildirimlerinizi bizimle paylaşabilirsiniz.'
:'You can share your opinions, improvement ideas, and general feedback about the site with us.'; ?>
</p>
</div>

<div class="section">
<div class="section-title"><?php echo $lang=='tr'?'Kullanıcı Şikayeti':'User Complaint'; ?></div>
<p>
<?php echo $lang=='tr'
?'Uygunsuz olduğunu düşündüğünüz kullanıcıları bildirmek için bu başlık altında bizimle iletişime geçebilirsiniz.'
:'You can contact us under this section to report users you consider inappropriate.'; ?>
</p>
</div>

<div class="mail">
<a href="mailto:info@uzay.info">info@uzay.info</a>
</div>

<div class="note">
<?php echo $lang=='tr'
?'Gönderilen e-postalar incelenir ve genellikle 72 saat içerisinde dönüş sağlanır.'
:'Submitted emails are reviewed and usually responded to within 72 hours.'; ?>
</div>
</div>

</body>
</html>
