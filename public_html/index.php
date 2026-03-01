<?php
session_start();
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
require_once('db_config.php');
$username = isset($_SESSION['username']) ? $_SESSION['username'] : ('<a href="/login" style="color: blue;">' . ($lang === 'tr' ? 'GİRİŞ' : 'SIGN IN') . '</a>');
$dropdownLinks = '';
if (isset($_SESSION['username']) && $lang === 'tr') {
    $dropdownLinks .= '
        <a href="/@/' . $username . '"><i class="fa-solid fa-user"></i> Profil</a>
        <a href="/yenisimulasyon">+ <i class="fa-solid fa-flask"></i> Simülasyon</a>
        <a href="/yeniozguransiklopedi">+ <i class="fa-solid fa-book"></i> Özgür Ansiklopedi</a>
        <a href="/yeniakademikmakale">+ <i class="fa-solid fa-graduation-cap"></i> Akademik Makale</a>
        <a href="/yeniogreticimakale">+ <i class="fa-solid fa-lightbulb"></i> Öğretici Makale</a>
        <a href="/yeniekitap">+ <i class="fa-solid fa-book-open"></i> E-Kitap</a>
        <a href="/yenikurs">+ <i class="fa-solid fa-chalkboard-teacher"></i> Kurs</a>
        <a href="/yeniforum">+ <i class="fa-solid fa-comments"></i> Forum</a>
        <a href="/settings"><i class="fa-solid fa-gear"></i> Ayarlar</a>
        <a href="/logout"><i class="fa-solid fa-right-from-bracket"></i> Çıkış</a>
    ';
}

if (isset($_SESSION['username']) && $lang === 'en') {
    $dropdownLinks .= '
        <a href="/@/' . $username . '"><i class="fa-solid fa-user"></i> Profile</a>
        <a href="/new_simulation">+ <i class="fa-solid fa-flask"></i> Simulation</a>
        <a href="/new_free_encyclopedia">+ <i class="fa-solid fa-book"></i> Free Encyclopedia</a>
        <a href="/new_academic_article">+ <i class="fa-solid fa-graduation-cap"></i> Academic Article</a>
        <a href="/new_tutorial_article">+ <i class="fa-solid fa-lightbulb"></i> Tutorial Article</a>
        <a href="/new_ebook">+ <i class="fa-solid fa-book-open"></i> E-Book</a>
        <a href="/new_course">+ <i class="fa-solid fa-chalkboard-teacher"></i> Course</a>
        <a href="/new_forum">+ <i class="fa-solid fa-comments"></i> Forum</a>
        <a href="/settings"><i class="fa-solid fa-gear"></i> Settings</a>
        <a href="/logout"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
    ';
}
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$is404 = ($path !== '/');

try { $stmt = $db->query("SELECT username FROM users ORDER BY username ASC"); $users = $stmt->fetchAll(PDO::FETCH_ASSOC); } catch (PDOException $e) { die("Veritabanı hatası: " . htmlspecialchars($e->getMessage())); }

require_once __DIR__.'/delete_inactive_users_auto.php';
require_once __DIR__.'/update_gram_gold_prices_auto.php';
require_once __DIR__.'/clean_expired_supports_auto.php';
require_once __DIR__.'/support_thank_auto.php';
require_once __DIR__.'/support_expired_auto.php';
require_once __DIR__.'/support_renew_check_auto.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<title id="dynamic-title"></title>
<link rel="shortcut icon" href="https://www.uzay.info/uzaylogo.ico">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
<style>
.logo{width:30px;height:30px;border-radius:50%;vertical-align:-7.5px}
.menubuton{background-color:#170a6e;color:white;padding:16px;font-size:16px;border:none;cursor:pointer;border-radius:8px}
.ayarlarbuton{position:absolute;font-family:Arial;top:15px;right:137px}
.ayarlarbuton:hover .menu-icerik{display:block}
.menu{position:relative;display:inline-block;font-family:Arial;font-size:15px}
.menu-icerik{display:none;position:absolute;background-color:#E6EBFF;min-width:165px;box-shadow:0px 8px 16px 0px rgba(0,0,0,0.2);border-radius:8px}
.menu-icerik a{color:black;padding:12px 16px;text-decoration:none;display:block}
.menu-icerik a:hover{background-color:#73B9FF}
.menu:hover .menu-icerik{display:block}
.menu:hover .menubuton{background-color:#3f37ff}
.baslik{color:blue;font-size:30px;font-family:verdana}
.butonkonum{position:absolute;right:180px;top:13px}
.butonlar{position:relative;left:800px;top:13px;all:unset;cursor:pointer;width:44px;height:44px}
form{background-color:#4654e1;width:300px;height:44px;border-radius:5px;display:flex;flex-direction:row;align-items:center}
input{all:unset;font:16px system-ui;color:#fff;height:100%;width:100%;padding:6px 10px}
::placeholder{color:#fff;opacity:0.7}
svg{color:#fff;fill:currentColor;width:24px;height:24px;padding:10px}
.user-list{width:284px;position:absolute;right:180px;top:61px;background:#170a6e;border-radius:10px;padding:6px 10px;box-shadow:0 8px 22px rgba(0,0,0,0.1);z-index:1000;display:none;max-height:337px;overflow-y:auto;font-family:"Poppins",sans-serif;backdrop-filter:blur(6px)}
a.user{display:block;padding:6px 10px;margin:4px 0;text-decoration:none;border-radius:8px;color:#7290e9;font-size:15px;font-weight:500;transition:all 0.2s ease;letter-spacing:0.3px}
a.user:hover{background:#4654e1;color:#fff;transform:translateX(3px)}
.empty{color:#7290e9;padding:8px 0;font-size:14px;text-align:center;font-style:italic}
.dropdown{position:absolute;display:inline-block;right:10px;top:12px}
.dropbtn{background-color:rgba(0,0,0,0);color:blue;border:none;cursor:pointer;padding:10px;font-size:20px;font-family:"Times New Roman",Times,serif}
.dropdown-content{display:none;position:absolute;background-color:rgba(0,0,0,0);min-width:160px;box-shadow:0px 8px 16px 0px blue;z-index:1;right:0}
.dropdown-content a{color:rgba(255,255,255,0.3);padding:12px 16px;text-decoration:none;display:block;text-align:right}
.dropdown-content a:hover{background-color:#000e44}
.dropdown:hover .dropdown-content{display:block}
.container{display:flex;justify-content:center;align-items:center;height:100vh}
.hamburger{display:none;cursor:pointer;font-size:28px;color:#3f37ff;position:absolute;top:18px;left:36px;background:none;border:none}
.hamburger i{transition:transform .35s ease,opacity .2s ease;font-size:24px;display:inline-block;}
.hamburger i.spin-right{transform:rotate(180deg);opacity:0;}
.hamburger i.spin-left{transform:rotate(-180deg);opacity:0;}
.lobbypanel{display:none;position:absolute;top:60px;left:36px;transform:translateX(-50%);background-color:#000032;color:white;border-radius:10px;overflow-y:auto;box-shadow:0 0 20px rgba(0,0,0,0.5);padding:20px;z-index:999}
.lobbypanel h3{margin-top:15px;color:#7dd3fc}
.lobbypanel a{display:block;padding:8px 0;color:white;text-decoration:none}
.lobbypanel a:hover{color:#1e3a8a}
.lobby-columns{display:flex;justify-content:space-between;flex-wrap:wrap;gap:20px;}
.lobby-left,.lobby-right{width:48%;display:flex;flex-direction:column;}
.lobbypanel a{color:white;text-decoration:none;margin:3px 0;}
.lobbypanel h3{margin-top:15px;color:#7dd3fc;}
.spacer{height:160px;}
.hero-gap{height:10px}
.mobil-ekstra{display: none;}
.ayarlarbuton .menu-icerik{z-index:1000}
.dropdown .dropdown-content{z-index:1000}
@media(max-width:1237px){.baslik{display:none}}
@media(max-width:1079px){.hamburger{display:block}.lobbypanel{width:500px;left:321px}.menu{display:none}.logo{position:relative; top:10.5px}.hero-gap{height:26px}}
@media(max-width:567px){.butonkonum{display:none}.mobil-ekstra{display: block;}}
@media(max-width:515px){.lobbypanel{width:298px;left:163px}.lobby-left,.lobby-right{width:100%;}.spacer{display:none;}}
</style>
</head>
<body>
<button class="hamburger"><i class="fa-solid fa-bars"></i></button>
<div class="lobbypanel" id="lobby">
	<div class="mobil-ekstra">
		<form role="search" id="form2" style="background-color:#4654e1;width:90%;margin:10px auto;border-radius:5px;display:flex;flex-direction:row;align-items:center">
			<input type="search" id="query2" name="q" placeholder="<?php echo $lang === 'en' ? 'Search...' : 'Ara...'; ?>" aria-label="<?php echo $lang === 'en' ? 'Search through site content' : 'Sitede ara'; ?>" style="all:unset;font:16px system-ui;color:#fff;height:100%;width:100%;padding:6px 10px">
			<button style="all:unset;cursor:pointer;width:44px;height:44px">
				<svg viewBox="0 0 1024 1024" style="color:#fff;fill:currentColor;width:24px;height:24px;padding:10px">
					<path d="M848.471 928l-263.059-263.059c-48.941 36.706-110.118 55.059-177.412 55.059-171.294 0-312-140.706-312-312s140.706-312 312-312c171.294 0 312 140.706 312 312 0 67.294-24.471 128.471-55.059 177.412l263.059 263.059-79.529 79.529zM189.623 408.078c0 121.364 97.091 218.455 218.455 218.455s218.455-97.091 218.455-218.455c0-121.364-103.159-218.455-218.455-218.455-121.364 0-218.455 97.091-218.455 218.455z"></path>
				</svg>
			</button>
		</form>
	</div>
	<?php if ($lang === 'en'): ?>
	<div class="lobby-columns">
		<div class="lobby-left">
			<h3>📘 Pedias</h3>
			<a href="/encyclopedia">Space Ency</a>
			<a href="/new_updates">New Discoveries</a>
			<a href="/space_arena">Space Arena</a>
			<a href="/@/SpacePedia">Our Studies</a>
			<a href="/free_encyclopedias">All Free Encyclopedias</a>
			<a href="/academic_articles">All Academic Articles</a>
			<a href="/tutorial_articles">All Tutorial Articles</a>
			<div class="spacer"></div>
			<h3>✨ Discover</h3>
			<a href="/ebooks">All E-Books</a>
			<a href="/courses">All Courses</a>
			<a href="/forums">All Forums</a>
			<a href="/games">Space Games</a>
		</div>
		<div class="lobby-right">
			<h3>🪐️ Simulations</h3>
			<a href="/simulations">All Simulations</a>
			<a href="/simulate">All Our Simulations</a>
			<a href="/simulate/planets">Planets</a>
			<a href="/simulate/stars">Stars</a>
			<a href="/simulate/galaxies">Galaxies</a>
			<a href="/simulate/blackholes">Black Holes</a>
			<a href="/simulate/nebulas">Nebulae</a>
			<a href="/simulate/comets">Comets</a>
			<a href="/simulate/neutrons">Neutron Stars</a>
			<a href="/simulate/deepspace">Deep Space</a>
			<a href="/simulate/constellations">Constellations</a>
			<h3>🤝 Support</h3>
            <a href="/support">Donate</a>
            <a href="/ads">Support Us by Watching Ads</a>
            <a href="/contact">Contact Us</a>
		</div>
	</div>
	<?php else: ?>
	<div class="lobby-columns">
		<div class="lobby-left">
			<h3>📘 Ansiklopediler</h3>
			<a href="/ansiklopedi">Uzay Ansiklopedisi</a>
			<a href="/yenigelismeler">Yeni Gelişmeler</a>
			<a href="/uzayarenası">Uzay Arenası</a>
			<a href="/@/uzayinfo">Çalışmalarımız</a>
			<a href="/ozguransiklopediler">Tüm Özgür Ansiklopediler</a>
			<a href="/akademikmakaleler">Tüm Akademik Makaleler</a>
			<a href="/ogreticimakaleler">Tüm Öğretici Makaleler</a>
			<div class="spacer"></div>
			<h3>✨ Keşfet</h3>
			<a href="/ekitaplar">Tüm E-Kitaplar</a>
			<a href="/kurslar">Tüm Kurslar</a>
			<a href="/forumlar">Tüm Forumlar</a>
			<a href="/oyunlar">Uzay Oyunları</a>
		</div>
		<div class="lobby-right">
			<h3>🪐️ Simülasyonlar</h3>
			<a href="/simulasyonlar">Tüm Simülasyonlar</a>
			<a href="/simulate">Tüm Simülasyonlarımız</a>
			<a href="/simulate/planets">Gezegenler</a>
			<a href="/simulate/stars">Yıldızlar</a>
			<a href="/simulate/galaxies">Galaksiler</a>
			<a href="/simulate/blackholes">Kara Delikler</a>
			<a href="/simulate/nebulas">Bulutsular</a>
			<a href="/simulate/comets">Kuyruklu Yıldızlar</a>
			<a href="/simulate/neutrons">Nötron Yıldızları</a>
			<a href="/simulate/deepspace">Derin Uzay</a>
			<a href="/simulate/constellations">Takım Yıldızları</a>
			<h3>🤝 Destek</h3>
            <a href="/support">Bağış Yapın</a>
            <a href="/ads">Reklam İzleyerek Destek Olun</a>
            <a href="/contact">İletişime Geçin</a>
		</div>
	</div>
	<?php endif; ?>
</div>
<div id="türkçe">
<img src="https://www.uzay.info/static/uzaylogo2.png" class="logo">
<b class="baslik">uzay.info</b>
<div class="menu">
  <button class="menubuton">📘 Ansiklopediler</button>
  <div class="menu-icerik">
    <a href="/ansiklopedi">Uzay Ansiklopedisi</a>
    <a href="/yenigelismeler">Yeni Gelişmeler</a>
    <a href="/uzayarenası">Uzay Arenası</a>
    <a href="/@/uzayinfo">Çalışmalarımız</a>
    <a href="/ozguransiklopediler">Tüm Özgür Ansiklopediler</a>
    <a href="/akademikmakaleler">Tüm Akademik Makaleler</a>
    <a href="/ogreticimakaleler">Tüm Öğretici Makaleler</a>
  </div>
</div>
<div class="menu">
  <button class="menubuton">🪐️ Simülasyonlar</button>
  <div class="menu-icerik">
    <a href="/simulasyonlar">Tüm Simülasyonlar</a>
    <a href="/simulate">Tüm Simülasyonlarımız</a>
    <a href="/simulate/planets">Gezegenler</a>
    <a href="/simulate/stars">Yıldızlar</a>
    <a href="/simulate/galaxies">Galaksiler</a>
    <a href="/simulate/blackholes">Kara Delikler</a>
    <a href="/simulate/nebulas">Bulutsular</a>
    <a href="/simulate/comets">Kuyruklu Yıldızlar</a>
    <a href="/simulate/neutrons">Nötron Yıldızları</a>
    <a href="/simulate/deepspace">Derin Uzay</a>
    <a href="/simulate/constellations">Takım Yıldızları</a>
  </div>
</div>
<div class="menu">
  <button class="menubuton">✨ Keşfet</button>
  <div class="menu-icerik">
    <a href="/ekitaplar">Tüm E-Kitaplar</a>
    <a href="/kurslar">Tüm Kurslar</a>
    <a href="/forumlar">Tüm Forumlar</a>
    <a href="/oyunlar">Uzay Oyunları</a>
  </div>
</div>
<div class="menu">
  <button class="menubuton">🤝 Destek</button>
  <div class="menu-icerik">
    <a href="/support">Bağış Yapın</a>
    <a href="/ads">Reklam İzleyerek Destek Olun</a>
    <a href="/contact">İletişime Geçin</a>
  </div>
</div>
<div class="ayarlarbuton">
<img src="https://www.uzay.info/static/ayarlar.png" width="40" height="40">
  <div class="menu-icerik">
    <a href="/tr">Türkçe</a>
    <a href="/eng">English</a>
  </div>
</div>
</div>
<div id="english">
<img src="https://www.uzay.info/static/uzaylogo2.png" class="logo">
<b class="baslik">SpacePedia</b>
<div class="menu">
  <button class="menubuton">📘 Pedias</button>
  <div class="menu-icerik">
    <a href="/encyclopedia">Space Ency</a>
    <a href="/new_updates">New Discoveries</a>
    <a href="/space_arena">Space Arena</a>
    <a href="/@/SpacePedia">Our Studies</a>
    <a href="/free_encyclopedias">All Free Encyclopedias</a>
    <a href="/academic_articles">All Academic Articles</a>
    <a href="/tutorial_articles">All Tutorial Articles</a>
  </div>
</div>
<div class="menu">
  <button class="menubuton">🪐️ Simulations</button>
  <div class="menu-icerik">
    <a href="/simulations">All Simulations</a>
    <a href="/simulate">All Our Simulations</a>
    <a href="/simulate/planets">Planets</a>
    <a href="/simulate/stars">Stars</a>
    <a href="/simulate/galaxies">Galaxies</a>
    <a href="/simulate/blackholes">Black Holes</a>
    <a href="/simulate/nebulas">Nebulae</a>
    <a href="/simulate/comets">Comets</a>
    <a href="/simulate/neutrons">Neutron Stars</a>
    <a href="/simulate/deepspace">Deep Space</a>
    <a href="/simulate/constellations">Constellations</a>
  </div>
</div>
<div class="menu">
  <button class="menubuton">✨ Discover</button>
  <div class="menu-icerik">
    <a href="/ebooks">All E-Books</a>
    <a href="/courses">All Courses</a>
    <a href="/forums">All Forums</a>
    <a href="/games">Space Games</a>
  </div>
</div>
<div class="menu">
  <button class="menubuton">🤝 Support</button>
  <div class="menu-icerik">
    <a href="/support">Donate</a>
    <a href="/ads">Support Us by Watching Ads</a>
    <a href="/contact">Contact Us</a>
  </div>
</div>
<div class="ayarlarbuton">
<img src="https://www.uzay.info/static/ayarlar.png" width="40" height="40">
  <div class="menu-icerik">
    <a href="/eng">English</a>
    <a href="/tr">Türkçe</a>
  </div>
</div>
</div>
<?php if (!$is404) { ?>
<div id="türkçe2">
<div class="hero-gap"></div>
<font face="Arial" size="6" color="blue">"Dünyanın İlk ve Tek Uzay Ansiklopedisi: uzay.info ile tanışın!"</font>
<font color="white">
<h2>uzay.info nedir?<h2>
<h3>uzay.info, dünyanın her noktasından erişilebilen özgür bir bilgi topluluğudur. Kullanımı tamamen ücretsiz olan bu platform, uzay hakkında kapsamlı bir ansiklopedik bilgi sunmayı hedefliyor. 
7'den 70'e herkesin uzay hakkında bilgi sahibi olmasını sağlamak için 7/24 aktif bir kaynak sunar.<h3>
<h2>Uzay Ansiklopedileri Hangi Gruplara Hitap Ediyor?</h2>
<h3>Hazırlanan Uzay Ansiklopedileri, öğrenmeden araştırmalara, araştırmalardan çalışmalara, çalışmalardan akademik makalelere kadar geniş bir yelpaze arayan kitleye hitap eder.</h3>
<h2>Eğlenceli Oyunlarla Öğretmeyi Amaçlıyoruz!</h2>
<h3>uzay.info, oyunlar aracılığıyla eğlendirerek öğretmeyi amaçlıyor. Ücretsiz oyunlarımızı oynayarak bilgi dağarcığınızı genişletebilir, destek bölümünde her türlü yardım alabilirsiniz. 
Ayrıca, özgür ansiklopedimize makale ve e-kitap yazarak katkıda bulunabilir, forumlar bölümünde güncel tartışmalara katılabilirsiniz. Sorularınızı ise iletişim bölümünden info@uzay.info adresine yazarak iletebilirsiniz!</h3>
<h2>uzay.info'yu Kullanmak Artık Çok Kolay!</h2>
<h3>Her kullanıcıya hitap eden uzay.info, ana sayfada verilen bölümlere tıklayarak kolayca kullanılabilir. Akademik makalelerimize /@/uzayinfo/akademikmakaleler adresinden ulaşabilirsiniz.</h3>
</font>
</div>
<div id="english2">
<div class="hero-gap"></div>
<font face="Arial" size="6" color="blue">"Meet spacepedia.info: the world's first and only Space Encyclopedia!"</font>
<font color="white">
<h2>What is spacepedia info?</h2>
<h3>spacepedia info is a free information community that can be accessed from any point in the world. This platform, which is completely free to use, aims to provide comprehensive encyclopedic information about space. It offers a 24/7 active resource to ensure that everyone from 7 to 70 has information about space.<h3>
<h2>To Which Groups Do Space Encyclopedias Address?</h2>
<h3>The prepared Space Encyclopedias appeal to a wide audience, ranging from learners to researchers, from research to studies, and from studies to academic articles.</h3>
<h2>We aims Teaching with Fun Games!</h2>
<h3>Spacepedia info aims to entertain and teach through games. You can expand your knowledge by playing our free games and get all kinds of help in the support section. 
You can also contribute to our free encyclopedia by writing articles and e-books, and participate in current discussions in the forums section. You can send your questions to info@uzay.info from the contact section!</h3>
<h2>Using spacepedia.info is Now Very Easy!</h2>
<h3> spacepedia.info, which appeals to every user, can be used easily by clicking on the sections provided on the home page. You can access our academic articles at /@/SpacePedia/academic_articles.</h3>
</font>
</div>
<?php } else { ?>
<div class="container"><img class="img" src="https://www.uzay.info/static/404notfound.png" alt="404 NOT FOUND"></div>
<?php } ?>
<form class="butonkonum" role="search" id="form">
    <input type="search" id="query" name="q" placeholder="<?php echo $lang === 'tr' ? 'Ara...' : 'Search...'; ?>" aria-label="<?php echo $lang === 'tr' ? 'Sitede ara' : 'Search through site content'; ?>" autocomplete="off">
    <button class="butonlar" id="searchButton" type="button">
        <svg viewBox="0 0 1024 1024">
            <path class="path1" d="M848.471 928l-263.059-263.059c-48.941 36.706-110.118 55.059-177.412 55.059-171.294 0-312-140.706-312-312s140.706-312 312-312c171.294 0 312 140.706 312 312 0 67.294-24.471 128.471-55.059 177.412l263.059 263.059-79.529 79.529zM189.623 408.078c0 121.364 97.091 218.455 218.455 218.455s218.455-97.091 218.455-218.455c0-121.364-103.159-218.455-218.455-218.455-121.364 0-218.455 97.091-218.455 218.455z"></path>
        </svg>
    </button>
</form>
<div class="user-list" id="userList"></div>
<div class="dropdown">
<button class="dropbtn" id="userBtn"><?php echo isset($_SESSION['username']) ? $username . ' <i class="fa-solid fa-user"></i>' : $username; ?></button>
<div class="dropdown-content"><?php echo $dropdownLinks; ?></div>
</div>
<script>
(function(){var b=document.querySelector(".hamburger"),p=document.getElementById("lobby");if(!b||!p)return;function setState(open){p.style.display=open?"block":"none";var i=b.querySelector("i");if(!i)return;if(open){if(i.classList.contains("fa-bars")){i.classList.add("spin-right");setTimeout(()=>{i.classList.replace("fa-bars","fa-xmark");i.classList.remove("spin-right")},175)}}else{i.classList.remove("spin-right");i.classList.remove("spin-left");if(i.classList.contains("fa-xmark")){i.classList.add("spin-left");setTimeout(()=>{i.classList.replace("fa-xmark","fa-bars");i.classList.remove("spin-left")},175)}}}b.addEventListener("click",function(){setState(p.style.display!=="block")});
function onResize(){if(window.innerWidth>1079&&p.style.display==="block")setState(false)}window.addEventListener("resize",onResize);onResize()})();
document.getElementById("searchButton").addEventListener("click", function(event){event.preventDefault();});
const users = <?php echo json_encode(array_map(fn($u) => $u['username'], $users)); ?>;
const input = document.getElementById("query");
const userList = document.getElementById("userList");
input.addEventListener("input", function(){const val=this.value.trim();userList.innerHTML="";if(val.length>=3){const matches=users.filter(u=>u.toLowerCase().startsWith(val.toLowerCase()));if(matches.length>0){matches.forEach(u=>{const a=document.createElement("a");a.className="user";a.href="/@/"+encodeURIComponent(u);a.textContent=u;userList.appendChild(a);});}else{const div=document.createElement("div");div.className="empty";div.textContent="<?php echo $lang==='tr'?'Eşleşen kullanıcı bulunamadı.':'No users found.'; ?>";userList.appendChild(div);}userList.style.display="block";}else{userList.style.display="none";}});
input.addEventListener("keydown", function(event){if(event.key==="Enter"){event.preventDefault();const val=this.value.trim();if(val.length<3){alert("<?php echo $lang==='tr'?'Lütfen en az 3 karakter giriniz.':'Please enter at least 3 characters.'; ?>");return;}const matches=users.filter(u=>u.toLowerCase().startsWith(val.toLowerCase()));if(matches.length>0){window.location.href="/@/"+encodeURIComponent(matches[0]);}else{alert("<?php echo $lang==='tr'?'Eşleşen kullanıcı bulunamadı.':'No users found.'; ?>");}}});
document.getElementById("form2").addEventListener("submit",e=>{e.preventDefault();let t=document.getElementById("query2").value.trim();if(t!=="")window.location.href="@/"+encodeURIComponent(t);});
function truncateByWidth(e,w){let o=e.textContent,t=o;e.textContent=t;if(e.offsetWidth<=w)return;for(;t.length>0;){t=t.slice(0,-1);e.textContent=t+"...";if(e.offsetWidth<=w)break}}
const userBtn=document.getElementById("userBtn");
if(userBtn&&!userBtn.querySelector("a")){let i=userBtn.querySelector("i"),iw=i?i.offsetWidth+6:0,mw=116-iw,n=userBtn.childNodes[0],s=document.createElement("span");s.textContent=n.textContent.replace(/\s+$/,"");userBtn.insertBefore(s,i);userBtn.insertBefore(document.createTextNode(" "),i);userBtn.removeChild(n);truncateByWidth(s,mw)}
var pageLang=document.documentElement.lang;var title=document.getElementById("dynamic-title");var baslikMetni;
if(pageLang==="tr"){baslikMetni="Dünyanın İlk ve Tek Uzay Ansiklopedisi: uzay.info ile tanışın!";document.getElementById("english").style.display="none";document.getElementById("english2").style.display="none";document.getElementById("türkçe").style.display="block";document.getElementById("türkçe2").style.display="block"}
if(pageLang==="en"){baslikMetni="Meet spacepedia.info: the world's first and only Space Encyclopedia!";document.getElementById("türkçe").style.display="none";document.getElementById("türkçe2").style.display="none";document.getElementById("english").style.display="block";document.getElementById("english2").style.display="block"}
else{baslikMetni="Dünyanın İlk ve Tek Uzay Ansiklopedisi: uzay.info ile tanışın!";document.getElementById("english").style.display="none";document.getElementById("english2").style.display="none";document.getElementById("türkçe").style.display="block";document.getElementById("türkçe2").style.display="block"}
title.textContent='"'+baslikMetni+'"';
</script>
</body>
</html>