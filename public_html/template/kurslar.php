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
<title>Tüm Kurslar</title>
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
.article-box { border: 1px solid #ddd; border-radius: 5px; width: calc(25% - 15px); background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); display: flex; flex-direction: column; transition: transform 0.3s; overflow: hidden; }
.article-box:hover { transform: scale(1.05); }
.article-box img { width: 100%; height: 180px; object-fit: cover; display: block; }
.article-info { padding: 10px; flex: 1; display: flex; flex-direction: column; }
.article-info h2 { font-size: 16px; margin: 2px 0; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
.article-info p { font-size: 12px; margin: 2px 0; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
.article-info a { font-size: 12px; text-decoration: none; color: #ff9800; }
.article-info a:hover { text-decoration: underline; }
.no-articles { display: flex; justify-content: center; align-items: center; height: 70vh; font-size: 22px; color: #555; font-weight: bold; text-align: center; }
@media (max-width: 1200px) { .article-box { width: calc(33.33% - 15px); } }
@media (max-width: 800px) { .article-box { width: calc(50% - 15px); } }
@media (max-width: 500px) { .article-box { width: calc(100% - 15px); } }
</style>
</head>
<body>
<header>
<a href="/" class="logo">uzay.info</a>
<h1>Tüm Kurslar</h1>
<form method="get" action="">
    <input type="text" name="search" placeholder="Kurs ara..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Ara</button>
</form>
</header>

<div class="container">
<?php
try {
    $params = [];
    $sql = "SELECT course_posts.*, users.username 
            FROM course_posts 
            LEFT JOIN users ON course_posts.user_id = users.id 
            WHERE 1=1";
    if ($search) {
        $sql .= " AND (
            course_posts.title LIKE :search 
            OR course_posts.description LIKE :search 
            OR course_posts.audience LIKE :search
            OR users.username LIKE :search
            OR DATE_FORMAT(course_posts.created_at, '%d-%m-%Y') LIKE :search
        )";
        $params[':search'] = "%$search%";
    }
    $sql .= " ORDER BY course_posts.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($courses) {
        foreach ($courses as $course) {
            $detail_link = '/kursdetay?id='.$course['id'];
            $tanitim_link = '/kurstanitim?id='.$course['id'];
            $video_thumb = 'https://uzay.info/static/video_placeholder.png';

            if (!empty($course['intro_video'])) {
                $intro = $course['intro_video'];
                if (strpos($intro,'youtube.com') !== false || strpos($intro,'youtu.be') !== false) {
                    preg_match('/(?:v=|\/)([a-zA-Z0-9_-]{11})/', $intro, $matches);
                    $video_id = $matches[1] ?? '';
                    if ($video_id) $video_thumb = 'https://img.youtube.com/vi/'.$video_id.'/0.jpg';
                }
            }

            echo '<div class="article-box">';
            echo '<a href="'.$detail_link.'" target="_blank"><img src="'.htmlspecialchars($video_thumb).'" alt="'.htmlspecialchars($course['title']).'"></a>';
            echo '<div class="article-info">';
            echo '<h2>'.htmlspecialchars($course['title']).'</h2>';
            echo '<p><b>Yazar:</b> '.htmlspecialchars($course['username']).'</p>';
            echo '<p><b>Açıklama:</b> '.htmlspecialchars($course['description']).'</p>';
            echo '<p><b>Hedef Kitle:</b> '.htmlspecialchars($course['audience']).'</p>';
            echo '<p><b>Bölüm Sayısı:</b> '.htmlspecialchars($course['section_count']).'</p>';
            if (!empty($course['intro_video'])) {
                echo '<p><a href="'.$tanitim_link.'" target="_blank">🎬 Tanıtım Videosunu İzle</a></p>';
            }
            echo '<p><a href="'.$detail_link.'" target="_blank">📺 Bölüm Videolarını İzle</a></p>';
            echo '</div></div>';
        }
    } else {
        echo "<div class='no-articles'>Arama kriterine uygun kurs bulunamadı.</div>";
    }
} catch(PDOException $e){
    echo "<div class='no-articles'>Veritabanı Hatası: ".$e->getMessage()."</div>";
}
?>
</div>
</body>
</html>
