<?php
session_start();
require_once('../db_config.php');

$username = isset($_GET['username']) ? trim($_GET['username']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$themeFilter = isset($_GET['theme']) ? intval($_GET['theme']) : 0;

function getThemeName($themeId){
    switch($themeId){
        case 13: return "Black Holes";
        case 12: return "Galaxies";
        case 11: return "Neutron Stars";
        case 10: return "Comets";
        case 9: return "Constellations";
        case 8: return "Stars";
        case 7: return "Planets";
        case 6: return "Nebulae";
        case 5: return "General Space";
        case 4: return "Accidents";
        case 3: return "Different Methods";
        case 2: return "Spacecraft";
        case 1: return "New Updates";
        default: return "Unknown";
    }
}

$themes = [
    1 => "New Updates",
    2 => "Spacecraft",
    3 => "Different Methods",
    4 => "Accidents",
    5 => "General Space",
    6 => "Nebulae",
    7 => "Planets",
    8 => "Stars",
    9 => "Constellations",
    10 => "Comets",
    11 => "Neutron Stars",
    12 => "Galaxies",
    13 => "Black Holes"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User's Tutorial Articles</title>
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
<h1>User's Tutorial Articles</h1>
<form method="get" action="">
    <select name="theme" onchange="this.form.submit()">
        <option value="">All Themes</option>
        <?php foreach($themes as $id => $name): ?>
            <option value="<?php echo $id; ?>" <?php if($themeFilter==$id) echo 'selected'; ?>><?php echo $name; ?></option>
        <?php endforeach; ?>
    </select>
    <input type="text" name="search" placeholder="Search for Article..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>
</header>

<div class="container">
<?php
try {
    $params = [':username'=>$username];
    $sql = "SELECT blog_posts2.*, users.username 
            FROM blog_posts2 
            LEFT JOIN users ON blog_posts2.user_id = users.id 
            WHERE users.username = :username
             AND users.account_closed = 0";

    if ($search) {
        $sql .= " AND (
            blog_posts2.title LIKE :search 
            OR blog_posts2.content LIKE :search 
            OR blog_posts2.subject LIKE :search
            OR blog_posts2.summary LIKE :search
        )";
        $params[':search'] = "%$search%";
    }

    if ($themeFilter) {
        $sql .= " AND blog_posts2.theme = :theme";
        $params[':theme'] = $themeFilter;
    }

    $sql .= " ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if($articles){
        foreach($articles as $article){
            $themeName = getThemeName($article['theme'] ?? 0);
            echo '<a class="article-box" target="_blank" href="/tutorial_article_detail?id='.urlencode($article['id']).'">';
            if (!empty($article['cover'])) echo '<img src="https://www.uzay.info/blog/'.htmlspecialchars($article['cover']).'" alt="'.htmlspecialchars($article['title']).'">';
            echo '<div class="article-info">';
            echo '<h2>'.htmlspecialchars($article['title']).'</h2>';
            echo '<p><b>Theme:</b> '.htmlspecialchars($themeName).'</p>';
            echo '<p><b>Subject:</b> '.htmlspecialchars($article['subject']).'</p>';
            echo '<p><b>Article Summary:</b> '.htmlspecialchars($article['summary']).'</p>';
            echo '<p><b>Date:</b> '.htmlspecialchars(date("d-m-Y", strtotime($article['created_at']))).'</p>';
            echo '</div></a>';
        }
    } else {
        echo "<div class='no-articles'>No results matched your search for article.</div>";
    }
}catch(PDOException $e){
    echo "<div class='no-articles'>Database Error: ".$e->getMessage()."</div>";
}
?>
</div>
</body>
</html>
