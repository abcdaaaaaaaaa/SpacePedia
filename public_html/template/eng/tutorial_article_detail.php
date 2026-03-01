<?php
session_start();
require_once('../db_config.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid article ID");
}

try {
    $stmt = $db->prepare("SELECT blog_posts2.*, users.username 
                          FROM blog_posts2 
                          LEFT JOIN users ON blog_posts2.user_id = users.id 
                          WHERE blog_posts2.id = :id");
    $stmt->execute([':id' => $id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        die("Article not found.");
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($article['title']); ?></title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; background:#f2f2f2 }
h1 { color: #072768; }
.summary { font-style: italic; color: #555; margin: 15px 0; }
.content { margin-top: 20px; }
</style>
</head>
<body>
<h1><?php echo htmlspecialchars($article['title']); ?></h1>
<p class="summary"><?php echo htmlspecialchars($article['summary']); ?></p>
<div class="content"><?php echo $article['content']; ?></div>
</body>
</html>
