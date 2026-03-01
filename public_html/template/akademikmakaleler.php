<?php
session_start();
require_once('../db_config.php');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tüm Akademik Makaleler</title>
<link rel="stylesheet" href="https://uzay.info/template/backgroundonly.css">
<style>
body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
header { background-color: #072768; color: white; padding: 0.6em 1em; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; }
header h1 { margin: 0; font-size: 20px; text-align: center; position: absolute; left: 50%; transform: translateX(-50%); }
header a.logo { color: white; text-decoration: none; font-weight: bold; font-size: 18px; margin-right: 15px; }
header form { margin: 0; display: flex; gap: 5px; align-items: center; }
header input[type="text"] { padding: 6px; border-radius: 5px; border: none; width: 200px; }
header button { padding: 6px 12px; border: none; border-radius: 5px; background: #ff9800; color: white; cursor: pointer; }
header button:hover { background: #e68900; }
.container { display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; }
.article-box { border: 1px solid #ddd; border-radius: 5px; width: calc(25% - 15px); aspect-ratio: 1 / 1; overflow: hidden; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); cursor: pointer; display: flex; flex-direction: column; transition: transform 0.3s; text-decoration: none; color: inherit; }
.article-box:hover { transform: scale(1.05); }
.article-box img { width: 100%; height: 50%; object-fit: cover; }
.article-info { padding: 10px; flex: 1; overflow: hidden; display: flex; flex-direction: column; }
.article-info h2 { font-size: 16px; margin: 2px 0; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
.article-info p { font-size: 12px; margin: 2px 0; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
.no-articles { display: flex; justify-content: center; align-items: center; height: 70vh; font-size: 22px; color: #555; font-weight: bold; text-align: center; }
@media (max-width: 1200px) { .article-box { width: calc(33.33% - 15px); } }
@media (max-width: 800px) { .article-box { width: calc(50% - 15px); } }
@media (max-width: 500px) { .article-box { width: calc(100% - 15px); } }
</style>
</head>
<body>
<header>
<a href="/" class="logo">uzay.info</a>
<h1>Tüm Akademik Makaleler</h1>
<form method="get" action="">
    <input type="text" name="search" placeholder="Makale ara..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Ara</button>
</form>
</header>

<div class="container">
<?php
try {
    $params = [];
    $sql = "SELECT article_posts.*, users.username 
            FROM article_posts 
            LEFT JOIN users ON article_posts.user_id = users.id 
            WHERE article_posts.visibility = 1";
    if ($search) {
        $sql .= " AND (
            article_posts.title LIKE :search 
            OR article_posts.subject LIKE :search 
            OR article_posts.summary LIKE :search
            OR article_posts.purpose LIKE :search
            OR article_posts.audience LIKE :search
            OR users.username LIKE :search
            OR DATE_FORMAT(article_posts.created_at, '%d-%m-%Y') LIKE :search
        )";
        $params[':search'] = "%$search%";
    }
    $sql .= " ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($articles) {
        foreach ($articles as $article) {
            $url = !empty($article['pdf']) ? 'https://www.uzay.info/article/'.htmlspecialchars($article['pdf']) : '#';
            echo '<a class="article-box" href="'.$url.'" target="_blank">';
            if (!empty($article['cover'])) echo '<img src="https://www.uzay.info/article/'.htmlspecialchars($article['cover']).'" alt="'.htmlspecialchars($article['title']).'">';
            echo '<div class="article-info">';
            echo '<h2>'.htmlspecialchars($article['title']).'</h2>';
            echo '<p><b>Yazar:</b> '.htmlspecialchars($article['username']).'</p>';
            echo '<p><b>Konu:</b> '.htmlspecialchars($article['subject']).'</p>';
            echo '<p><b>Özet:</b> '.htmlspecialchars($article['summary']).'</p>';
            echo '<p><b>Tarih:</b> '.htmlspecialchars(date("d-m-Y", strtotime($article['created_at']))).'</p>';
            echo '</div></a>';
        }
    } else {
        echo "<div class='no-articles'>Arama kriterine uygun akademik makale bulunamadı.</div>";
    }
} catch (PDOException $e) {
    echo "<div class='no-articles'>Veritabanı Hatası: ".$e->getMessage()."</div>";
}
?>
</div>
</body>
</html>
