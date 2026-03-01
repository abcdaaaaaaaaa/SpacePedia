<?php
require_once('../db_config.php');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Free Encyclopedias</title>
<link rel="stylesheet" href="https://uzay.info/template/backgroundonly.css">
<style>
body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
header { background-color: #072768; color: white; padding: 0.6em 1em; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; gap: 10px; }
header h1 { margin: 0; font-size: 20px; text-align: center; position: absolute; left: 50%; transform: translateX(-50%); }
header a.logo { color: white; text-decoration: none; font-weight: bold; font-size: 18px; }
header form { margin: 0; display: flex; gap: 5px; align-items: center; }
header input[type="text"] { padding: 6px; border-radius: 5px; border: none; width: 220px; }
header button { padding: 6px 12px; border: none; border-radius: 5px; background: #ff9800; color: white; cursor: pointer; }
header button:hover { background: #e68900; }
.add-btn { padding: 6px 12px; border: none; border-radius: 5px; background: #4CAF50; color: white; cursor: pointer; text-decoration: none; }
.add-btn:hover { background: #3b8c40; }
.container { display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; }
.article-box { border: 1px solid #ddd; border-radius: 5px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); cursor: pointer; display: flex; flex-direction: column; padding: 10px; transition: transform 0.3s; text-decoration: none; color: inherit; width: calc(25% - 15px); }
.article-box:hover { transform: scale(1.03); }
.article-info h2 { font-size: 16px; margin: 0 0 5px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.article-info p { font-size: 12px; margin: 0; color: #555; }
.no-articles { display: flex; justify-content: center; align-items: center; height: 70vh; font-size: 22px; color: #555; font-weight: bold; text-align: center; }
@media (max-width: 1200px) { .article-box { width: calc(33.33% - 15px); } }
@media (max-width: 800px) { .article-box { width: calc(50% - 15px); } }
@media (max-width: 500px) { .article-box { width: calc(100% - 15px); } }
</style>
</head>
<body>
<header>
  <div style="display:flex; align-items:center; gap:10px;">
    <a href="/" class="logo">spacepedia.info</a>
    <a href="/new_free_encyclopedia" target="_blank" class="add-btn">+ New Free Encyclopedia</a>
  </div>
  <h1>All Free Encyclopedias</h1>
  <form method="get" action="">
      <input type="text" name="search" placeholder="Search for Free Encyclopedia..." value="<?php echo htmlspecialchars($search); ?>">
      <button type="submit">Search</button>
  </form>
</header>

<div class="container">
<?php
try {
    $params = [];
    $sql = "SELECT * FROM encyclopedia_posts2";
    if ($search) {
        $sql .= " WHERE (
            title LIKE :search 
            OR content LIKE :search 
            OR DATE_FORMAT(created_at, '%d-%m-%Y') LIKE :search
            OR DATE_FORMAT(updated_at, '%d-%m-%Y') LIKE :search
        )";
        $params[':search'] = "%$search%";
    }
    $sql .= " ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($articles) {
        foreach ($articles as $article) {
            echo '<a class="article-box" target="_blank" href="/free_encyclopedia_detail?id='.$article['id'].'">';
            echo '<div class="article-info">';
            echo '<h2>'.htmlspecialchars($article['title']).'</h2>';
            echo '<p><b>Created on</b> '.htmlspecialchars(date("d-m-Y", strtotime($article['created_at']))).'</p>';
            echo '<p><b>Last Updated:</b> '.htmlspecialchars(date("d-m-Y", strtotime($article['updated_at']))).'</p>';
            echo '</div></a>';
        }
    } else {
        echo "<div class='no-articles'>No results matched your search for free encyclopedias.</div>";
    }
} catch (PDOException $e) {
    echo "<div class='no-articles'>Database Error: ".$e->getMessage()."</div>";
}
?>
</div>
</body>
</html>
