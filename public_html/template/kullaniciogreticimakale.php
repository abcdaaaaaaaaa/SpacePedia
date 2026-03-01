<?php
session_start();
require_once('../db_config.php');

$username = isset($_GET['username']) ? trim($_GET['username']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$themeFilter = isset($_GET['theme']) ? intval($_GET['theme']) : 0;

function getThemeName($themeId){
    switch($themeId){
        case 13: return "Kara Delikler";
        case 12: return "Galaksiler";
        case 11: return "Nötron Yıldızları";
        case 10: return "Kuyruklu Yıldızlar";
        case 9: return "Takım Yıldızları";
        case 8: return "Yıldızlar";
        case 7: return "Gezegenler";
        case 6: return "Bulutsular";
        case 5: return "Genel Uzay";
        case 4: return "Kazalar";
        case 3: return "Farklı Yöntemler";
        case 2: return "Uzay Araçları";
        case 1: return "Yeni Gelişmeler";
        default: return "Bilinmeyen";
    }
}

$themes = [
    1 => "Yeni Gelişmeler",
    2 => "Uzay Araçları",
    3 => "Farklı Yöntemler",
    4 => "Kazalar",
    5 => "Genel Uzay",
    6 => "Bulutsular",
    7 => "Gezegenler",
    8 => "Yıldızlar",
    9 => "Takım Yıldızları",
    10 => "Kuyruklu Yıldızlar",
    11 => "Nötron Yıldızları",
    12 => "Galaksiler",
    13 => "Kara Delikler"
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tüm Öğretici Makaleleri</title>
<link rel="stylesheet" href="https://uzay.info/template/backgroundonly.css">
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; }
header { background-color:#072768; color:white; padding:0.6em 1em; margin-bottom:15px; display:flex; align-items:center; justify-content:space-between; position:relative; }
header h1 { margin:0; font-size:20px; text-align:center; position:absolute; left:50%; transform:translateX(-50%); }
header a.logo { color:white; text-decoration:none; font-weight:bold; font-size:18px; margin-right:15px; }
header form { margin:0; display:flex; gap:5px; align-items:center; }
header input[type="text"], header select { padding:6px; border-radius:5px; border:none; }
header input[type="text"] { width:150px; }
header select { width:150px; cursor:pointer; }
header button { padding:6px 12px; border:none; border-radius:5px; background:#ff9800; color:white; cursor:pointer; }
header button:hover { background:#e68900; }
.container { display:flex; flex-wrap:wrap; justify-content:center; gap:15px; }
.article-box { position:relative; border:1px solid #ddd; border-radius:5px; width:calc(25% - 15px); aspect-ratio:1/1; overflow:hidden; background:white; box-shadow:0 0 10px rgba(0,0,0,0.1); cursor:pointer; display:flex; flex-direction:column; transition:transform 0.3s; text-decoration:none; color:inherit; }
.article-box:hover { transform:scale(1.05); }
.article-box img { width:100%; height:50%; object-fit:cover; display:block; }
.article-info { padding:10px; flex:1; overflow:hidden; display:flex; flex-direction:column; }
.article-info h2 { font-size:16px; margin:2px 0; white-space:nowrap; text-overflow:ellipsis; overflow:hidden; }
.article-info p { font-size:12px; margin:2px 0; white-space:nowrap; text-overflow:ellipsis; overflow:hidden; }
.no-articles { display:flex; justify-content:center; align-items:center; height:70vh; font-size:22px; color:#555; font-weight:bold; text-align:center; }
@media(max-width:1200px){.article-box{width:calc(33.33% - 15px);}}
@media(max-width:800px){.article-box{width:calc(50% - 15px);}}
@media(max-width:500px){.article-box{width:calc(100% - 15px);}}
</style>
</head>
<body>
<header>
<a href="/@/<?php echo urlencode($username); ?>" class="logo"><?php echo htmlspecialchars($username); ?></a>
<h1>Tüm Öğretici Makaleleri</h1>
<form method="get" action="">
    <select name="theme" onchange="this.form.submit()">
        <option value="">Tüm Temalar</option>
        <?php foreach($themes as $id => $name): ?>
            <option value="<?php echo $id; ?>" <?php if($themeFilter==$id) echo 'selected'; ?>><?php echo $name; ?></option>
        <?php endforeach; ?>
    </select>
    <input type="text" name="search" placeholder="Makale ara..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Ara</button>
</form>
</header>

<div class="container">
<?php
try {
    $params = [':username'=>$username];
    $sql = "SELECT blog_posts.*, users.username 
            FROM blog_posts 
            LEFT JOIN users ON blog_posts.user_id = users.id 
            WHERE users.username = :username
             AND users.account_closed = 0";

    if ($search) {
        $sql .= " AND (
            blog_posts.title LIKE :search 
            OR blog_posts.content LIKE :search 
            OR blog_posts.subject LIKE :search
            OR blog_posts.summary LIKE :search
        )";
        $params[':search'] = "%$search%";
    }

    if ($themeFilter) {
        $sql .= " AND blog_posts.theme = :theme";
        $params[':theme'] = $themeFilter;
    }

    $sql .= " ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if($articles){
        foreach($articles as $article){
            $themeName = getThemeName($article['theme'] ?? 0);
            echo '<a class="article-box" target="_blank" href="/ogreticimakaledetay?id='.urlencode($article['id']).'">';
            if (!empty($article['cover'])) echo '<img src="https://www.uzay.info/blog/'.htmlspecialchars($article['cover']).'" alt="'.htmlspecialchars($article['title']).'">';
            echo '<div class="article-info">';
            echo '<h2>'.htmlspecialchars($article['title']).'</h2>';
            echo '<p><b>Tema:</b> '.htmlspecialchars($themeName).'</p>';
            echo '<p><b>Konu:</b> '.htmlspecialchars($article['subject']).'</p>';
            echo '<p><b>Makale Özeti:</b> '.htmlspecialchars($article['summary']).'</p>';
            echo '<p><b>Tarih:</b> '.htmlspecialchars(date("d-m-Y", strtotime($article['created_at']))).'</p>';
            echo '</div></a>';
        }
    } else {
        echo "<div class='no-articles'>Arama kriterine uygun makale bulunamadı.</div>";
    }
}catch(PDOException $e){
    echo "<div class='no-articles'>Veritabanı Hatası: ".$e->getMessage()."</div>";
}
?>
</div>
</body>
</html>
