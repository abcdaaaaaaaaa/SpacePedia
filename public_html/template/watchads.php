<?php
session_start();
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
$isLoggedIn=isset($_SESSION['username']);

$L=[
'tr'=>[
'title'=>'Reklam İzleyerek Destek Olun',
'badge_text'=>'Bu alanda tamamen kendi isteğinizle reklam izleyerek siteye destek olabilirsiniz.<br>İlginizi çeken içerikler olursa tamamen kendi tercihinizle inceleyebilirsiniz.',
'frame_title'=>'Reklamlar Alanı',
'thanks'=>'Değerli desteğiniz için çok teşekkür ederiz 💙',
'slot_160x300'=>'160 × 300 Reklam Alanı',
'slot_160x600'=>'160 × 600 Reklam Alanı',
'slot_300x250'=>'300 × 250 Reklam Alanı',
'slot_320x50'=>'320 × 50 Reklam Alanı',
'slot_468x60'=>'468 × 60 Reklam Alanı',
'slot_728x90'=>'728 × 90 Reklam Alanı',
'extra_container'=>'Mini Reklamlar Alanı',
'direct_title'=>'Destek Linki',
'direct_button'=>'Reklamı Aç',
'cookie_notice'=>'Bu sayfada gösterilen reklamlar, <a href="https://adsterra.com" style="color:royalblue">Adsterra</a> tarafından sunulur (yetişkinlere yönelik reklam modu devre dışı bırakılacak şekilde ayarlanarak) ve bu reklamlar kapsamında çerezler <a href="https://adsterra.com" style="color:royalblue">Adsterra</a> tarafından sadece <a href="/ads" style="color:royalblue">o sayfayı</a> kullandığınız süre içerisinde anlık olarak kullanılabilir. Bu kullanım, reklamların sunulması, performans ölçümü, güvenlik ve kötüye kullanımın önlenmesi gibi belli amaçlar için gerçekleşebilir. Bu sayfada, üçüncü taraf bir reklam hizmeti faaliyet gösterebilir. Tamam’a basarak bu sayfadaki reklam hizmetinin çalışmasını ve bu kapsamda gerçekleşebilecek teknik işlemleri kabul etmiş olursunuz.<br><br> Değerli desteğiniz için çok teşekkür ederiz 💙',
'cookie_button'=>'Tamam'
],
'en'=>[
'title'=>'Support by Watching Ads',
'badge_text'=>'In this area, you can voluntarily support the site by watching advertisements.<br>If any content interests you, you may explore it entirely by your own choice.',
'frame_title'=>'Advertisements Area',
'thanks'=>'Thank you so much for your valuable support 💙',
'slot_160x300'=>'160 × 300 Advertisement Area',
'slot_160x600'=>'160 × 600 Advertisement Area',
'slot_300x250'=>'300 × 250 Advertisement Area',
'slot_320x50'=>'320 × 50 Advertisement Area',
'slot_468x60'=>'468 × 60 Advertisement Area',
'slot_728x90'=>'728 × 90 Advertisement Area',
'extra_container'=>'Mini Advertisements Area',
'direct_title'=>'Support Link',
'direct_button'=>'Open Advertisement',
'cookie_notice'=>'The advertisements shown on this page are provided by <a href="https://adsterra.com" style="color:royalblue">Adsterra</a> with adult-oriented advertising mode disabled, and within this advertising process cookies may be used instantly by <a href="https://adsterra.com" style="color:royalblue">Adsterra</a> only while you are using <a href="/ads" style="color:royalblue">that page</a>. This use may take place for certain purposes such as delivering advertisements, measuring performance, security, and preventing abuse. A third-party advertising service may operate on this page. By pressing OK, you agree to the operation of the advertising service on this page and the related technical processes.<br><br> Thank you so much for your valuable support 💙',
'cookie_button'=>'OK'
]
];

$ADS=[
'tr'=>[
'a160x300'=>['key'=>'4a9e9686351fc446d711f1af711dfa88','width'=>160,'height'=>300],
'a160x600'=>['key'=>'a2c15eecfd607211cf91b1b862e7ccf2','width'=>160,'height'=>600],
'a300x250'=>['key'=>'10951b9ef39c59e981735e996a7dec14','width'=>300,'height'=>250],
'a320x50'=>['key'=>'8214dbe7902bb8cacf2ea19a21d57cc6','width'=>320,'height'=>50],
'a468x60'=>['key'=>'fd1299940970f84fd1009e33c9727ede','width'=>468,'height'=>60],
'a728x90'=>['key'=>'09dcc0ac822f5578f3621795920ff797','width'=>728,'height'=>90],
'extra_invoke'=>'https://pl28912608.effectivegatecpm.com/38c3a6ce2b287c29eb3ed30dde0ec64b/invoke.js',
'extra_id'=>'container-38c3a6ce2b287c29eb3ed30dde0ec64b',
'direct_url'=>'https://www.effectivegatecpm.com/ymrwtjnze?key=21671ef84cb620581bf9c1d1f266ad0a',
'footer_scripts'=>[
'https://pl28912581.effectivegatecpm.com/8b/90/62/8b906221d3de0ba1d4849876af05d425.js',
'https://pl28912624.effectivegatecpm.com/dc/bd/2e/dcbd2e434608a3a6b730a8c31ef599b2.js'
]
],
'en'=>[
'a160x300'=>['key'=>'24f6b2218aa170cddac386f8e61d7532','width'=>160,'height'=>300],
'a160x600'=>['key'=>'480c4d3b902b5e707498e616f3651e72','width'=>160,'height'=>600],
'a300x250'=>['key'=>'5502da8075aba8f7bbb15da8a2f54801','width'=>300,'height'=>250],
'a320x50'=>['key'=>'69d2436afece351721111f1cee67cc53','width'=>320,'height'=>50],
'a468x60'=>['key'=>'0c00822043d42905e5a49b0a1bb5a002','width'=>468,'height'=>60],
'a728x90'=>['key'=>'6cbb9846da452ac8b02cb23341aebdb5','width'=>728,'height'=>90],
'extra_invoke'=>'https://pl28915102.effectivegatecpm.com/e66bf864b8a9f25c9fddcdea8e7d8def/invoke.js',
'extra_id'=>'container-e66bf864b8a9f25c9fddcdea8e7d8def',
'direct_url'=>'https://www.effectivegatecpm.com/fvj8mzxk?key=b2edb1905efe0a11289d60b9bb5e6d7c',
'footer_scripts'=>[
'https://pl28915096.effectivegatecpm.com/0e/bd/b8/0ebdb8722443b5d1d8f262cfa76368de.js',
'https://pl28915106.effectivegatecpm.com/d1/fa/99/d1fa99236d1bb69d0e26dfb14b01f2f6.js'
]
]
];

$A=$ADS[$lang];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $L[$lang]['title']; ?></title>
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
<style>
html,body{margin:0;padding:0;width:100%;min-height:100%;overflow:auto}
body{display:flex;justify-content:center;align-items:flex-start}
body.notice-open{overflow:hidden}
.container{max-width:1100px;width:calc(100% - 30px);background:#1e1e1e;color:#fff;padding:34px;border-radius:18px;box-shadow:0 0 30px rgba(255,255,255,.25);transition:.35s;text-align:center;margin:40px 15px}
.container:hover{box-shadow:0 0 55px rgba(0,170,255,.7),0 0 110px rgba(0,170,255,.35)}
h1{margin:0 0 14px 0}
.badge-wrap{width:90%;max-width:980px;margin:18px auto 10px auto;display:flex;justify-content:center}
.badge{position:relative;display:inline-flex;align-items:center;gap:10px;padding:12px 18px;border-radius:999px;background:#6600ff;color:#fff;font-weight:800;letter-spacing:.2px;box-shadow:0 0 22px rgba(102,0,255,.6)}
.badge:before{content:"";position:absolute;inset:-2px;border-radius:999px;background:radial-gradient(circle at center,#6600ff 0%,rgba(102,0,255,.9) 50%,rgba(102,0,255,.6) 80%,rgba(102,0,255,.25) 100%);filter:blur(10px);opacity:.9;z-index:-1}
.badge i{color:#fff;filter:drop-shadow(0 0 8px rgba(255,255,255,.6))}
.badge span{font-size:14px;opacity:.98;line-height:1.9;display:block}
.ad-frame{margin-top:16px;padding:20px;background:#111;border-radius:16px;box-shadow:0 0 18px rgba(0,0,0,.6)}
.ad-title{color:#00aaff;font-weight:700;margin-bottom:18px}
.ads-grid{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:18px;align-items:start}
.ad-card{background:#161616;border:1px solid rgba(0,170,255,.18);border-radius:14px;padding:16px;display:flex;flex-direction:column;align-items:center;justify-content:flex-start}
.ad-card-title{font-size:13px;color:#9fdcff;margin-bottom:12px;font-weight:700}
.ad-slot{display:flex;align-items:center;justify-content:center;overflow:hidden;background:#0d0d0d;border:1px dashed rgba(0,170,255,.28);border-radius:12px;position:relative}
.ad-160x300{width:160px;height:300px}
.ad-160x600{width:160px;height:600px}
.ad-300x250{width:300px;height:250px}
.ad-320x50{width:320px;height:50px}
.ad-468x60{width:468px;height:60px}
.ad-728x90{width:728px;height:90px}
.span-3{grid-column:span 3}
.span-4{grid-column:span 4}
.span-6{grid-column:span 6}
.span-12{grid-column:span 12}
.full-width-center{display:flex;justify-content:center;align-items:center;width:100%}
.direct-link-wrap{width:100%;display:flex;justify-content:center}
.direct-link-btn{display:inline-flex;align-items:center;justify-content:center;min-width:220px;padding:14px 24px;border-radius:12px;background:linear-gradient(135deg,#00aaff,#0a6cff);color:#fff;text-decoration:none;font-weight:700;transition:.25s;box-shadow:0 0 20px rgba(0,170,255,.22)}
.direct-link-btn:hover{transform:translateY(-2px);box-shadow:0 0 28px rgba(0,170,255,.38)}
.thanks{margin-top:26px;font-size:16px;font-weight:700}
.top-notice-overlay{position:fixed;inset:0;background:rgba(0,0,0,.82);z-index:9998}
.top-notice{position:fixed;top:18px;left:50%;transform:translateX(-50%);width:min(960px,calc(100% - 24px));background:#111;border:1px solid rgba(0,170,255,.35);border-radius:18px;padding:20px 20px 18px 20px;box-shadow:0 0 35px rgba(0,170,255,.28);z-index:9999;color:#fff}
.top-notice-text{font-size:15px;line-height:1.8;text-align:left}
.top-notice-actions{display:flex;justify-content:flex-end;margin-top:16px}
.top-notice-btn{border:none;border-radius:12px;padding:12px 22px;background:linear-gradient(135deg,#00aaff,#0a6cff);color:#fff;font-weight:700;cursor:pointer;box-shadow:0 0 20px rgba(0,170,255,.22)}
.top-notice-btn:hover{transform:translateY(-1px)}
@media(max-width:1024px){
.ads-grid{grid-template-columns:repeat(6,minmax(0,1fr))}
.span-12,.span-6,.span-4,.span-3{grid-column:span 6}
.ad-728x90,.ad-468x60,.ad-320x50,.ad-300x250,.ad-160x600,.ad-160x300{max-width:100%}
}
@media(max-width:768px){
.container{width:calc(100% - 20px);margin:20px 10px;padding:18px}
.badge-wrap{width:100%;margin:14px auto 8px auto}
.badge{padding:12px 14px;border-radius:24px}
.badge span{font-size:13px;line-height:1.8}
.ads-grid{grid-template-columns:1fr}
.span-12,.span-6,.span-4,.span-3{grid-column:span 1}
.ad-728x90{width:100%;max-width:728px;height:90px}
.ad-468x60{width:100%;max-width:468px;height:60px}
.ad-320x50{width:100%;max-width:320px;height:50px}
.ad-300x250{width:100%;max-width:300px;height:250px}
.ad-160x600{width:160px;height:600px}
.ad-160x300{width:160px;height:300px}
.top-notice{top:10px;width:calc(100% - 16px);padding:16px}
.top-notice-text{font-size:14px;line-height:1.7}
.top-notice-actions{justify-content:center}
.top-notice-btn{width:100%}
}
</style>
</head>
<body<?php echo !$isLoggedIn ? ' class="notice-open"' : ''; ?>>

<?php if(!$isLoggedIn){ ?>
<div class="top-notice-overlay" id="topNoticeOverlay"></div>
<div class="top-notice" id="topNotice">
<div class="top-notice-text"><?php echo $L[$lang]['cookie_notice']; ?></div>
<div class="top-notice-actions">
<button type="button" class="top-notice-btn" id="topNoticeButton"><?php echo $L[$lang]['cookie_button']; ?></button>
</div>
</div>
<?php } ?>

<div class="container">
<h1><?php echo $L[$lang]['title']; ?></h1>

<div class="badge-wrap">
<div class="badge"><span><?php echo $L[$lang]['badge_text']; ?></span></div>
</div>

<div class="ad-frame">
<div class="ad-title"><?php echo $L[$lang]['frame_title']; ?></div>

<div class="ads-grid">
<div class="ad-card span-12">
<div class="ad-card-title"><?php echo $L[$lang]['extra_container']; ?></div>
<div class="full-width-center">
<div id="<?php echo $A['extra_id']; ?>"></div>
</div>
</div>

<div class="ad-card span-12">
<div class="ad-card-title"><?php echo $L[$lang]['direct_title']; ?></div>
<div class="direct-link-wrap">
<a class="direct-link-btn" href="<?php echo $A['direct_url']; ?>" target="_blank" rel="noopener sponsored"><?php echo $L[$lang]['direct_button']; ?></a>
</div>
</div>

<div class="ad-card span-12">
<div class="ad-card-title"><?php echo $L[$lang]['slot_728x90']; ?></div>
<div class="full-width-center">
<div class="ad-slot ad-728x90" id="slot-728x90"></div>
</div>
</div>

<div class="ad-card span-6">
<div class="ad-card-title"><?php echo $L[$lang]['slot_320x50']; ?></div>
<div class="ad-slot ad-320x50" id="slot-320x50"></div>
</div>

<div class="ad-card span-6">
<div class="ad-card-title"><?php echo $L[$lang]['slot_468x60']; ?></div>
<div class="ad-slot ad-468x60" id="slot-468x60"></div>
</div>

<div class="ad-card span-3">
<div class="ad-card-title"><?php echo $L[$lang]['slot_160x600']; ?></div>
<div class="ad-slot ad-160x600" id="slot-160x600"></div>
</div>

<div class="ad-card span-3">
<div class="ad-card-title"><?php echo $L[$lang]['slot_160x300']; ?></div>
<div class="ad-slot ad-160x300" id="slot-160x300"></div>
</div>

<div class="ad-card span-6">
<div class="ad-card-title"><?php echo $L[$lang]['slot_300x250']; ?></div>
<div class="ad-slot ad-300x250" id="slot-300x250"></div>
</div>
</div>
</div>

<div class="thanks"><?php echo $L[$lang]['thanks']; ?></div>
</div>

<script>
const adsConfig=<?php echo json_encode([
'extra_invoke'=>$A['extra_invoke'],
'footer_scripts'=>$A['footer_scripts'],
'a728x90'=>$A['a728x90'],
'a320x50'=>$A['a320x50'],
'a468x60'=>$A['a468x60'],
'a160x600'=>$A['a160x600'],
'a160x300'=>$A['a160x300'],
'a300x250'=>$A['a300x250']
],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
let adsLoaded=false;

function loadExternalScript(src,asyncValue=true){
return new Promise(function(resolve,reject){
const s=document.createElement('script');
s.src=src;
if(asyncValue)s.async=true;
s.onload=resolve;
s.onerror=reject;
document.body.appendChild(s);
});
}

function loadAdIntoSlot(slotId,ad){
return new Promise(function(resolve,reject){
const slot=document.getElementById(slotId);
if(!slot){resolve();return;}
window.atOptions={'key':ad.key,'format':'iframe','height':ad.height,'width':ad.width,'params':{}};
const s=document.createElement('script');
s.src='https://www.highperformanceformat.com/'+ad.key+'/invoke.js';
s.onload=resolve;
s.onerror=reject;
slot.appendChild(s);
});
}

async function startAds(){
if(adsLoaded)return;
adsLoaded=true;
await loadExternalScript(adsConfig.extra_invoke,true);
await loadAdIntoSlot('slot-728x90',adsConfig.a728x90);
await loadAdIntoSlot('slot-320x50',adsConfig.a320x50);
await loadAdIntoSlot('slot-468x60',adsConfig.a468x60);
await loadAdIntoSlot('slot-160x600',adsConfig.a160x600);
await loadAdIntoSlot('slot-160x300',adsConfig.a160x300);
await loadAdIntoSlot('slot-300x250',adsConfig.a300x250);
await loadExternalScript(adsConfig.footer_scripts[0],true);
await loadExternalScript(adsConfig.footer_scripts[1],true);
}
</script>

<?php if($isLoggedIn){ ?>
<script>
startAds();
</script>
<?php }else{ ?>
<script>
document.getElementById('topNoticeButton').addEventListener('click',async function(){
document.body.classList.remove('notice-open');
document.getElementById('topNotice').style.display='none';
document.getElementById('topNoticeOverlay').style.display='none';
await startAds();
});
</script>
<?php } ?>

</body>
</html>
