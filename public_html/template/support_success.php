<?php
session_start();
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}

$siteName=$lang==='tr'?'uzay.info':'spacepedia.info';

$L=[
'tr'=>[
'title'=>'Bağışı Tamamlama Alanı',
'desc'=>'Küçük veya büyük her katkı, bu platformun sürdürülebilirliği ve gelişimi için çok önemlidir.',
'important'=>'Lütfen bağış sırasında aşağıdaki adımları eksiksiz uygulayınız',
'steps'=>'1) "Name or @yoursocial" alanına <strong>sadece '.$siteName.' kullanıcı adını</strong> yazınız.<br>
2) "Say something nice..." alanına <strong>Web sitesine gönüllü katkı.</strong> yazınız.<br>
3) "Make this message private" seçeneğini işaretleyiniz.<br>
4) İkonunuz süreliyse ve süresi dolmadan aylık olarak otomatik yenilenmesini istiyorsanız "Make this monthly" seçeneğini işaretleyiniz.<br>
5) Bağışı kendi adınıza veya başka bir kullanıcı adına yapabilirsiniz. İkon, yazılan kullanıcı adına tanımlanacaktır.',
'mini_legal'=>'Bağış işlemi tamamlandıktan sonra ikonunuz genellikle 72 saat içerisinde manuel olarak aktif edilir. Süresi dolan ikonlar 48–72 saat içerisinde sistem tarafından otomatik olarak kaldırılır. Bu platform tamamen ücretsizdir ve bağışlar gönüllülük esasına dayanır, herhangi bir hizmet veya erişim ayrıcalığı sağlamaz. Profil ikonu yalnızca teşekkür amacıyla gösterilir.',
'button'=>'Bağışı Tamamlayın',
'thanks'=>'Değerli desteğiniz için çok teşekkür ederiz 💙'
],
'en'=>[
'title'=>'Donation Completion Area',
'desc'=>'Every contribution, small or large, is very important for the sustainability and development of this platform.',
'important'=>'Please follow the steps below during donation',
'steps'=>'1) In the "Name or @yoursocial" field, write <strong>only the '.$siteName.' username</strong>.<br>
2) In the "Say something nice..." field, write <strong>Voluntary contribution to the website.</strong><br>
3) Check the "Make this message private" option.<br>
4) If your icon is time-based and you want it to renew monthly before it expires, check the "Make this monthly" option.<br>
5) You may donate in your own name or on behalf of another user. The icon will be assigned to the username provided.',
'mini_legal'=>'After the donation is completed, your icon will usually be manually activated within 72 hours. Expired icons are automatically removed within 48–72 hours. This platform is completely free and donations are voluntary, they do not grant any service or access privileges. The profile icon is displayed purely as a token of appreciation.',
'button'=>'Complete the Donation',
'thanks'=>'Thank you so much for your valueable support 💙'
]
];
?>
<!DOCTYPE HTML>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $L[$lang]['title']; ?></title>

<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">

<style>
html,body{margin:0;padding:0;width:100%;height:100%;overflow:auto;}
body{display:flex;justify-content:center;align-items:flex-start;}
.container{max-width:720px;width:100%;background:#1e1e1e;color:#fff;padding:30px;border-radius:16px;box-shadow:0 0 25px rgba(255,255,255,.25);transition:.35s;text-align:center;margin:40px 15px;}
.container:hover{box-shadow:0 0 45px rgba(0,170,255,.7),0 0 90px rgba(0,170,255,.35)}
h1{margin-bottom:12px}
.desc{font-size:15px;line-height:1.6;margin:0}
.note{margin-top:18px;padding:18px;background:#072768;border-radius:14px;font-size:14px;line-height:1.6;text-align:left}
.legal{margin-top:14px;padding:14px 16px;background:#111;border-radius:14px;font-size:13.5px;line-height:1.6;opacity:.95;text-align:left;box-shadow:inset 0 0 0 1px rgba(255,255,255,.08)}
.thanks{margin-top:18px;font-size:17px;font-weight:700;text-align:center}
.link-btn{display:block;margin:22px auto 0 auto;max-width:520px;padding:16px 18px;border-radius:12px;background:#111;text-decoration:none;text-align:center;color:#00aaff;font-weight:700;box-shadow:inset 0 0 0 1px rgba(255,255,255,.08);transition:.25s}
.link-btn:hover{box-shadow:0 0 22px rgba(0,170,255,.55),inset 0 0 0 1px rgba(0,170,255,.35)}
</style>
</head>
<body>

<div class="container">
<h1><?php echo $L[$lang]['title']; ?></h1>

<div class="desc"><?php echo $L[$lang]['desc']; ?></div>

<a class="link-btn" href="https://buymeacoffee.com/abcda" target="_blank">
<?php echo $L[$lang]['button']; ?>
</a>

<div class="note">
<strong><?php echo $L[$lang]['important']; ?>:</strong><br>
<?php echo $L[$lang]['steps']; ?>
</div>

<div class="legal">
<?php echo $L[$lang]['mini_legal']; ?>
</div>

<div class="thanks">
<?php echo $L[$lang]['thanks']; ?>
</div>
</div>

</body>
</html>