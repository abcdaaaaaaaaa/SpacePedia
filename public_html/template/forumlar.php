<?php
session_start();
require_once('../db_config.php');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$author = isset($_GET['author']) ? trim($_GET['author']) : '';
$themeFilter = isset($_GET['theme']) ? intval($_GET['theme']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

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
<title>Tüm Forumlar</title>
<link rel="stylesheet" href="https://uzay.info/template/backgroundonly.css">
<style>
body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
header { background-color: #072768; color: white; padding: 0.6em 1em; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; }
header h1 { margin: 0; font-size: 20px; text-align: center; position: absolute; left: 50%; transform: translateX(-50%); }
header a.logo { color: white; text-decoration: none; font-weight: bold; font-size: 18px; margin-right: 10px; }
header .left { display: flex; align-items: center; gap: 10px; }
header .right { display: flex; flex-direction: column; gap: 5px; align-items: flex-end; }
header select, header input[type="text"] { padding: 6px; border-radius: 5px; border: none; }
header select { cursor: pointer; }
header button { padding: 6px 12px; border: none; border-radius: 5px; background: #ff9800; color: white; cursor: pointer; }
header button:hover { background: #e68900; }
.container { display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; }
.post-box { position: relative; border: 1px solid #ddd; border-radius: 5px; width: calc(25% - 15px); aspect-ratio: 2 / 1; overflow: hidden; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); cursor: pointer; display: flex; flex-direction: column; transition: transform 0.3s; }
.post-box:hover { transform: scale(1.05); }
.post-info { padding: 10px; flex: 1; overflow: hidden; display: flex; flex-direction: column; }
.post-info h2 { font-size: 16px; margin: 2px 0; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
.post-info p { font-size: 12px; margin: 2px 0; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
.no-posts { display: flex; justify-content: center; align-items: center; height: 70vh; font-size: 22px; color: #555; font-weight: bold; text-align: center; }
@media (max-width: 1200px) { .post-box { width: calc(33.33% - 15px); } }
@media (max-width: 800px) { .post-box { width: calc(50% - 15px); } }
@media (max-width: 500px) { .post-box { width: calc(100% - 15px); } }
</style>
</head>
<body>
<header>
  <div class="left">
    <a href="/" class="logo">uzay.info</a>
    <form method="get" action="">
      <select name="theme" onchange="this.form.submit()">
        <option value="">Tüm Temalar</option>
        <?php foreach($themes as $id => $name): ?>
          <option value="<?php echo $id; ?>" <?php if($themeFilter==$id) echo 'selected'; ?>><?php echo $name; ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>
  <h1>Tüm Forumlar</h1>
  <div class="right">
    <form method="get" action="">
      <input type="text" name="search" placeholder="Forum ara..." value="<?php echo htmlspecialchars($search); ?>">
      <button type="submit" name="action" value="forum">Ara</button>
    </form>
    <form method="get" action="">
      <input type="text" name="author" placeholder="Yazar ara..." value="<?php echo htmlspecialchars($author); ?>">
      <button type="submit" name="action" value="author">Ara</button>
    </form>
  </div>
</header>
<div class="container">
<?php
try {
    $params = [];
    $sql = "SELECT forum_posts.*, users.username,
            (SELECT message FROM forum_messages WHERE post_id = forum_posts.id ORDER BY created_at DESC LIMIT 1) AS last_message,
            (SELECT user_id FROM forum_messages WHERE post_id = forum_posts.id ORDER BY created_at DESC LIMIT 1) AS last_user_id,
            (SELECT created_at FROM forum_messages WHERE post_id = forum_posts.id ORDER BY created_at DESC LIMIT 1) AS last_message_time
            FROM forum_posts
            LEFT JOIN users ON forum_posts.user_id = users.id
            WHERE 1";
    if ($themeFilter) {
        $sql .= " AND forum_posts.theme = :theme";
        $params[':theme'] = $themeFilter;
    }
    if ($action === "forum" && $search) {
        $sql .= " AND (forum_posts.title LIKE :search OR (SELECT message FROM forum_messages WHERE post_id = forum_posts.id ORDER BY created_at DESC LIMIT 1) LIKE :search)";
        $params[':search'] = "%$search%";
    }
    if ($action === "author" && $author) {
        $sql .= " AND users.username LIKE :author";
        $params[':author'] = "%$author%";
    }
    $sql .= " ORDER BY forum_posts.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($posts) {
        foreach ($posts as $post) {
            $themeName = getThemeName($post['theme'] ?? 0);
            $lastUsername = '';
            if ($post['last_user_id']) {
                $stmtUser = $db->prepare("SELECT username FROM users WHERE id = :uid");
                $stmtUser->bindValue(':uid', $post['last_user_id'], PDO::PARAM_INT);
                $stmtUser->execute();
                $lastUser = $stmtUser->fetch(PDO::FETCH_ASSOC);
                $lastUsername = $lastUser['username'];
            }
            echo '<div class="post-box" data-id="'.$post['id'].'">';
            echo '<div class="post-info">';
            echo '<h2>'.htmlspecialchars($post['title']).'</h2>';
            echo '<p><b>Oluşturan:</b> '.htmlspecialchars($post['username']).'</p>';
            echo '<p><b>Tema:</b> '.htmlspecialchars($themeName).'</p>';
            echo '<p><b>Son Mesaj:</b> '.htmlspecialchars($post['last_message'] ?? '').'</p>';
            echo '<p><b>Son Mesaj Tarihi:</b> '.htmlspecialchars($post['last_message_time'] ? date("d-m-Y H:i", strtotime($post['last_message_time'])) : '').'</p>';
            echo '<p><b>Son Mesajı Gönderen:</b> '.htmlspecialchars($lastUsername).'</p>';
            echo '</div></div>';
        }
    } else {
        echo "<div class='no-posts'>Arama kriterine uygun forum postu bulunamadı.</div>";
    }
} catch (PDOException $e) {
    echo "<div class='no-posts'>Veritabanı Hatası: ".$e->getMessage()."</div>";
}
?>
</div>
<script>
document.querySelectorAll('.post-box').forEach(box => {
    box.onclick = () => {
        window.location.href = '/forumdetay?post_id=' + box.dataset.id;
    };
});
</script>
</body>
</html>
