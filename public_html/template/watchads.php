<?php
session_start();
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}

$L=[
'tr'=>[
'title'=>'Reklam İzleyerek Destek Olun',
'desc'=>'Bu alanda tamamen kendi isteğinizle reklam izleyerek siteye destek olabilirsiniz.',
'note'=>'Değerli desteğinizin geçerli olabilmesi için <strong>REKLAMA TIKLAMANIZ</strong> gerekmektedir.',
'frame_title'=>'Reklam Alanı',
'info'=>'Bu reklam alanı bilerek sade tutulmuştur. Amaç dikkatinizin dağılmadan reklamı görüntülemenizdir.',
'thanks'=>'Değerli desteğiniz için çok teşekkür ederiz 💙'
],
'en'=>[
'title'=>'Support by Watching Ads',
'desc'=>'In this area, you can voluntarily support the site by watching advertisements.',
'note'=>'To make your valuable support valid, you must <strong>CLICK ON THE ADVERTISEMENT</strong>.',
'frame_title'=>'Advertisement Area',
'info'=>'This advertisement area is intentionally kept simple to avoid distractions.',
'thanks'=>'Thank you so much for your valuable support 💙'
]
];
?>
<!DOCTYPE HTML>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9872958805289346"
     crossorigin="anonymous"></script>
<title><?php echo $L[$lang]['title']; ?></title>

<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">

<style>
html,body{margin:0;padding:0;width:100%;height:100%;overflow:auto;}
body{display:flex;justify-content:center;align-items:flex-start;}
.container{max-width:780px;width:100%;background:#1e1e1e;color:#fff;padding:34px;border-radius:18px;box-shadow:0 0 30px rgba(255,255,255,.25);transition:.35s;text-align:center;margin:40px 15px}
.container:hover{box-shadow:0 0 55px rgba(0,170,255,.7),0 0 110px rgba(0,170,255,.35)}
h1{margin-bottom:18px}
.desc{font-size:15px;line-height:1.6;margin-bottom:14px}
.note{margin-top:18px;padding:16px;background:#072768;border-radius:14px;font-size:14px;line-height:1.6}
.ad-frame{margin-top:28px;padding:20px;background:#111;border-radius:16px;box-shadow:0 0 18px rgba(0,0,0,.6)}
.ad-title{color:#00aaff;font-weight:700;margin-bottom:12px}
.ad-box{width:100%;height:250px;border:2px dashed #00aaff;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:14px;opacity:.85}
.info{margin-top:18px;font-size:14px;opacity:.9}
.thanks{margin-top:26px;font-size:16px;font-weight:700}
</style>
</head>
<body>

<div class="container">
<h1><?php echo $L[$lang]['title']; ?></h1>

<div class="desc"><?php echo $L[$lang]['desc']; ?></div>

<div class="note"><?php echo $L[$lang]['note']; ?></div>

<div class="ad-frame">
<div class="ad-title"><?php echo $L[$lang]['frame_title']; ?></div>

<div class="ad-box">
<?php echo $lang=='tr'?'Reklam burada gösterilecektir':'Advertisement will be displayed here'; ?>
</div>
</div>

<div class="info"><?php echo $L[$lang]['info']; ?></div>

<div class="thanks"><?php echo $L[$lang]['thanks']; ?></div>
</div>

</body>
</html>
