<?php
session_start();
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
require_once('db_config.php');

$L = [
    'tr' => [
        'profile_not_found' => '../KullanıcıBulunamadı',
        'account_closed' => 'Hesap Kapatılmıştır.',
        'account_banned' => 'Bu Hesap SpacePedia/Uzay Platformunun Kullanım Şartlarını İhlâl Ettiği İçin Kapatılmıştır.',
        'no_info' => 'Henüz bir bilgi eklenmedi.',
        'edit_profile' => 'Profilini Düzenle',
        'home' => 'Ana Sayfa',
        'all_my_works' => 'Tüm Çalışmalarım:',
        'all_works' => 'Tüm Çalışmaları:',
        'academic' => 'Tüm Akademik Makaleler',
        'tutorial' => 'Tüm Öğretici Makaleler',
        'simulation' => 'Tüm Simülasyonlar',
        'ebook' => 'Tüm e-Kitaplar',
        'course' => 'Tüm Kurslar',
        'light' => '☀️ Aydınlık Mod',
        'dark' => '🌙 Karanlık Mod',
        'verified' => 'Doğrulanmış Hesap',
        'last_online' => 'Son çevrim içi'
    ],
    'en' => [
        'profile_not_found' => '../UserNotFound',
        'account_closed' => 'This Account Is Closed.',
        'account_banned' => 'This Account Has Been Closed Due To Violation Of SpacePedia/Uzay Platform Terms Of Use.',
        'no_info' => 'No information added yet.',
        'edit_profile' => 'Edit Profile',
        'home' => 'Home',
        'all_my_works' => 'All My Works:',
        'all_works' => 'All Works:',
        'academic' => 'All Academic Articles',
        'tutorial' => 'All Tutorial Articles',
        'simulation' => 'All Simulations',
        'ebook' => 'All e-Books',
        'course' => 'All Courses',
        'light' => '☀️ Light Mode',
        'dark' => '🌙 Dark Mode',
        'verified' => 'Verified Account',
        'last_online' => 'Last online'
    ]
];

$paths = [
    'tr' => [
        'academic' => 'akademikmakaleler',
        'tutorial' => 'ogreticimakaleler',
        'simulation' => 'simulasyonlar',
        'ebook' => 'ekitaplar',
        'course' => 'kurslar'
    ],
    'en' => [
        'academic' => 'academic_articles',
        'tutorial' => 'tutorial_articles',
        'simulation' => 'simulations',
        'ebook' => 'ebooks',
        'course' => 'courses'
    ]
];

$closedMessage = '';
$closedType = '';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['username'])) {
    $requestedUsername = $_GET['username'];
    $stmt=$db->prepare("SELECT id,username,email,profile_info,profile_image,verified,last_online,supporter,support_start,mode,account_closed FROM users WHERE username=:username");
    $stmt->bindParam(':username', $requestedUsername);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        header("Location: " . $L[$lang]['profile_not_found']);
        exit();
    }
    if((int)$user['account_closed']===1){
        $closedMessage=$L[$lang]['account_closed'];
        $closedType='closed';
    }elseif((int)$user['account_closed']===-1){
        $closedMessage=$L[$lang]['account_banned'];
        $closedType='banned';
    }
}

$isLoggedInUser = isset($_SESSION['username']) && $_SESSION['username'] === ($user['username'] ?? '');

if(isset($_SESSION['user_id'])){$db->prepare("UPDATE users SET last_online=NOW() WHERE id=?")->execute([$_SESSION['user_id']]);}
function lastOnlineText($d,$l){$n=time();if(!$d)return $l==='tr'?'Bilinmiyor':'Unknown';$t=strtotime($d);if(!$t)return $l==='tr'?'Bilinmiyor':'Unknown';$f=$n-$t;if($f<120)return $l==='tr'?'Şimdi':'Now';if($f<3600){$m=floor($f/60);return $l==='tr'?$m.' dakika önce':$m.' minutes ago';}if($f<86400){$h=floor($f/3600);return $l==='tr'?$h.' saat önce':$h.' hours ago';}if($f<2592000){$g=floor($f/86400);return $l==='tr'?$g.' gün önce':$g.' days ago';}if($f<31536000){$a=floor($f/2592000);return $l==='tr'?$a.' ay önce':$a.' months ago';}$y=floor($f/31536000);return $l==='tr'?$y.' yıl önce':$y.' years ago';}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($user['username'] ?? ''); ?></title>
<link rel="shortcut icon" href="https://www.uzay.info/uzaylogo.ico">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
<style>
body{font-family:'Roboto',sans-serif;margin:0;padding:0;transition:background .3s,color .3s}
.container{margin:40px auto;padding:25px;background-color:rgba(102,0,255,0.8);border-radius:16px;box-shadow:0 8px 30px rgba(0,0,0,0.1)}
.profile{text-align:center;margin-bottom:30px}
.profile img{width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid #000e44;margin-bottom:15px}
.profile h1{font-size:28px;color:#000e44;margin:10px 0}
.profile p{color:#555;font-size:15px;margin-bottom:10px}
.profile .email{color:#555;font-size:14px;font-weight:500;margin-bottom:10px}
.edit-btn{display:inline-block;margin-top:10px;padding:8px 16px;background:#072768;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;transition:all .3s}
.edit-btn:hover{background:#0a3ca8}
.studies h2{font-size:22px;color:#000e44;margin-bottom:20px}
.studies .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px}
.card{background:#fff;border-radius:14px;padding:20px;text-align:center;transition:all .35s ease;box-shadow:0 6px 15px rgba(0,0,0,0.1);cursor:pointer}
.card i{font-size:36px;margin-bottom:12px;color:#fff;padding:20px;border-radius:50%;display:inline-block}
.card span{display:block;font-weight:600;color:#333;margin-top:5px}
.card:hover{transform:translateY(-8px);box-shadow:0 10px 25px rgba(0,0,0,0.2)}
.academic i{background:#1abc9c}.tutorial i{background:#3498db}.simulation i{background:#f39c12}.ebook i{background:#9b59b6}.course i{background:#e74c3c}
body.dark-mode{color:#ddd}
body.dark-mode .container{background:#1e1e1e;color:#ddd}
body.dark-mode .profile h1{color:#fff}
body.dark-mode .profile p,body.dark-mode .profile .email{color:#aaa}
body.dark-mode .edit-btn{background:#3498db}
body.dark-mode .card{background:#2a2a2a;box-shadow:0 6px 20px rgba(0,0,0,0.5)}
body.dark-mode .card span{color:#fff}
.dark-mode-toggle{position:fixed;top:20px;right:20px;background:#072768;color:#fff;border:none;border-radius:20px;padding:8px 16px;cursor:pointer;font-size:14px;transition:all .3s}
.dark-mode-toggle:hover{background:#0a3ca8}
.home-btn{position:fixed;top:20px;left:20px;background:#00aaff;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:500;z-index:999;box-shadow:0 4px 12px rgba(0,0,0,0.2);transition:all .3s}
.home-btn:hover{background:#0088cc}
.badges{margin-top:12px;display:flex;justify-content:center}
.badge{position:relative;font-size:22px;cursor:pointer;transition:transform .35s ease,filter .35s ease;margin:0 5px}
.badge i{display:inline-block;transition:all .3s ease}
.badge:hover{transform:translateY(-6px) scale(1.2);filter:drop-shadow(0 0 24px currentColor)}
.badge::after{content:attr(data-tooltip);position:absolute;bottom:-36px;left:50%;transform:translateX(-50%);background:#000e44;color:#fff;padding:6px 12px;border-radius:8px;white-space:nowrap;font-size:12px;opacity:0;pointer-events:none;transition:opacity .3s ease,transform .3s ease}
.badge:hover::after{opacity:1;transform:translateX(-50%) translateY(4px)}
.support-1{color:#c0c0c0;filter:drop-shadow(0 0 6px rgba(192,192,192,.6))}
.support-2{color:#2ecc71;filter:drop-shadow(0 0 8px rgba(46,204,113,.7))}
.support-3{color:#3498db;filter:drop-shadow(0 0 10px rgba(52,152,219,.75))}
.support-4{color:#9b59b6;filter:drop-shadow(0 0 12px rgba(155,89,182,.8))}
.support-5{color:#f1c40f;filter:drop-shadow(0 0 14px rgba(241,196,15,.85))}
.support-6{color:#e74c3c;filter:drop-shadow(0 0 18px rgba(231,76,60,.95))}
.support-7{color:#e74c3c;filter:drop-shadow(0 0 18px rgba(231,76,60,.95))}
#verified{color:#1e90ff;filter:drop-shadow(0 0 18px rgba(30,144,255,.9))}
.last-online{margin-top:8px;font-size:13px;color:#ddd}
.closed-box{text-align:center;padding:35px 20px}
.closed-box h1{font-size:30px;color:#fff;margin:10px 0 14px}
.closed-box p{font-size:17px;line-height:1.6;max-width:760px;margin:0 auto}
.closed-box.closed .closed-symbol{width:84px;height:84px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;background:rgba(255,255,255,.18);border:2px solid rgba(255,255,255,.28);backdrop-filter:blur(4px)}
.closed-box.closed .closed-symbol i{font-size:34px;color:#fff}
.closed-box.closed p{color:#f0f0f0}
.closed-box.banned .closed-symbol{width:84px;height:84px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;background:rgba(255,0,0,.12);border:2px solid rgba(255,80,80,.35);backdrop-filter:blur(4px)}
.closed-box.banned .closed-symbol i{font-size:34px;color:#ff8a8a}
.closed-box.banned p{color:#ffb3b3}
</style>
</head>
<body>
<?php if($isLoggedInUser && $closedMessage===''): ?>
<button class="dark-mode-toggle" onclick="toggleDarkMode()"></button>
<?php endif; ?>
<a href="/" class="home-btn"><i class="fas fa-home"></i> <?php echo $L[$lang]['home']; ?></a>
<div class="container">
<?php if($closedMessage!==''): ?>
    <div class="closed-box <?php echo $closedType==='banned'?'banned':'closed'; ?>">
        <div class="closed-symbol">
            <i class="fa-solid <?php echo $closedType==='banned'?'fa-triangle-exclamation':'fa-user-lock'; ?>"></i>
        </div>
        <h1><?php echo htmlspecialchars($user['username']); ?></h1>
        <p><?php echo htmlspecialchars($closedMessage); ?></p>
    </div>
<?php else: ?>
    <div class="profile">
        <img src="https://uzay.info/<?php echo htmlspecialchars($user['profile_image'] ?? "profile_images/uzaylogo.png"); ?>" alt="Profil Resmi">
        <h1><?php echo htmlspecialchars($user['username']); ?></h1>
        <p class="email"><?php echo htmlspecialchars($user['email']); ?></p>

        <div class="badges">
            <?php if($user['verified']==1): ?>
                <span class="badge" id="verified" data-tooltip="<?php echo $L[$lang]['verified']; ?>"><i class="fa-regular fa-circle-check"></i></span>
            <?php endif; ?>
            <?php if($user['supporter']>0): ?>
                <span class="badge support-<?php echo (int)$user['supporter']; ?>" data-tooltip="<?php echo $lang==='tr' ? date('d.m.Y',strtotime($user['support_start'])).'’den beri Destekçi' : 'Supporter since '.date('d.m.Y',strtotime($user['support_start'])); ?>"><i class="fa-solid fa-gem"></i></span>
            <?php endif; ?>
        </div>

        <div class="last-online">
            <?php echo $L[$lang]['last_online'].': '.(time()-strtotime($user['last_online'])<120?($lang==='tr'?'Şimdi':'Now'):lastOnlineText($user['last_online'],$lang)); ?>
        </div>

        <p><?php echo htmlspecialchars($user['profile_info'] ?? $L[$lang]['no_info']); ?></p>
        <?php if ($isLoggedInUser): ?>
            <a href="/profile_edit" class="edit-btn"><i class="fas fa-user-edit"></i> <?php echo $L[$lang]['edit_profile']; ?></a>
        <?php endif; ?>
    </div>

    <div class="studies">
        <h2><?php echo $isLoggedInUser ? $L[$lang]['all_my_works'] : $L[$lang]['all_works']; ?></h2>
        <div class="cards">
            <a class="card academic" href="/@/<?php echo urlencode($user['username']); ?>/<?php echo $paths[$lang]['academic']; ?>" target="_blank"><i class="fa-solid fa-graduation-cap"></i><span><?php if ($lang === 'tr') echo $L[$lang]['academic'] . ($isLoggedInUser ? 'im' : 'i'); else echo ($isLoggedInUser ? 'All My ' : 'All ') . substr($L[$lang]['academic'], 4); ?></span></a>
            <a class="card tutorial" href="/@/<?php echo urlencode($user['username']); ?>/<?php echo $paths[$lang]['tutorial']; ?>" target="_blank"><i class="fa-solid fa-lightbulb"></i><span><?php if ($lang === 'tr') echo $L[$lang]['tutorial'] . ($isLoggedInUser ? 'im' : 'i'); else echo ($isLoggedInUser ? 'All My ' : 'All ') . substr($L[$lang]['tutorial'], 4); ?></span></a>
            <a class="card simulation" href="/@/<?php echo urlencode($user['username']); ?>/<?php echo $paths[$lang]['simulation']; ?>" target="_blank"><i class="fa-solid fa-flask"></i><span><?php if ($lang === 'tr') echo $L[$lang]['simulation'] . ($isLoggedInUser ? 'ım' : 'ı'); else echo ($isLoggedInUser ? 'All My ' : 'All ') . substr($L[$lang]['simulation'], 4); ?></span></a>
            <a class="card ebook" href="/@/<?php echo urlencode($user['username']); ?>/<?php echo $paths[$lang]['ebook']; ?>" target="_blank"><i class="fa-solid fa-book-open"></i><span><?php if ($lang === 'tr') echo $L[$lang]['ebook'] . ($isLoggedInUser ? 'ım' : 'ı'); else echo ($isLoggedInUser ? 'All My ' : 'All ') . substr($L[$lang]['ebook'], 4); ?></span></a>
            <a class="card course" href="/@/<?php echo urlencode($user['username']); ?>/<?php echo $paths[$lang]['course']; ?>" target="_blank"><i class="fa-solid fa-chalkboard-teacher"></i><span><?php if ($lang === 'tr') echo $L[$lang]['course'] . ($isLoggedInUser ? 'ım' : 'ı'); else echo ($isLoggedInUser ? 'All My ' : 'All ') . substr($L[$lang]['course'], 4); ?></span></a>
        </div>
    </div>
<?php endif; ?>
</div>
<script>
function applyTheme(theme){
    <?php if($closedType==='banned'): ?>
    document.body.classList.add("dark-mode");
    return;
    <?php endif; ?>

    document.body.classList.toggle("dark-mode",theme==="dark");
    const btn=document.querySelector(".dark-mode-toggle");
    if(btn) btn.textContent=theme==="dark"?"<?php echo $L[$lang]['light']; ?>":"<?php echo $L[$lang]['dark']; ?>";
}
(function(){
    const dbTheme="<?php echo (($user['mode']??'light')==='dark'?'dark':'light'); ?>";
    applyTheme(dbTheme);
})();
async function setThemeOnServer(theme){
    const r=await fetch("/theme_update.php",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:"mode="+encodeURIComponent(theme)});
    const t=await r.text();
    try{const j=JSON.parse(t);return j&&j.ok===true;}catch(e){return false;}
}
async function toggleDarkMode(){
    const current=document.body.classList.contains("dark-mode")?"dark":"light";
    const next=current==="dark"?"light":"dark";
    applyTheme(next);
    const ok=await setThemeOnServer(next);
    if(!ok){
        applyTheme(current);
        alert("<?php echo $lang==='tr'?'Tema kaydedilemedi.':'Theme could not be saved.'; ?>");
    }
}
</script>
</body>
</html>
