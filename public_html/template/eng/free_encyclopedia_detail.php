<?php
session_start();
require_once('../db_config.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) die("Invalid Encyclopedia ID");

try {
    $stmt = $db->prepare("SELECT * FROM encyclopedia_posts2 WHERE id=:id");
    $stmt->execute([':id'=>$id]);
    $encyclopedia = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$encyclopedia) die("Free encyclopedia entry not found.");
} catch(PDOException $e){
    die("Database Error: ".$e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($encyclopedia['title']); ?></title>
<style>
body { font-family: Arial, sans-serif; margin: 0; padding: 0; background:#ecebe8; }
header { background-color: #f2f2f2; color: white; padding: 0.6em 1em; display:flex; align-items:center; justify-content:space-between; position: sticky; top:0; z-index:1000; }
header h1 { margin:0; font-size:20px; text-align:center; flex:1; }
header .edit-btn, header .delete-btn { padding:6px 12px; border-radius:5px; text-decoration:none; color:white; font-weight:bold; }
header .edit-btn { background-color:#4CAF50; }
header .edit-btn:hover { background-color:#3b8c40; }
header .delete-btn { background-color:#f44336; }
header .delete-btn:hover { background-color:#d32f2f; }
.content { padding:20px; }
</style>
</head>
<body>

<header>
    <a href="/edit_free_encyclopedia?id=<?php echo $encyclopedia['id']; ?>" class="edit-btn">Edit</a>
    <h1 style="color: #ff9800;"><?php echo htmlspecialchars($encyclopedia['title']); ?></h1>
    <a href="/delete_encyclopedia_post.php?id=<?php echo $encyclopedia['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this encyclopedia entry?');">Delete</a>
</header>

<div class="content">
    <div><?php echo $encyclopedia['content']; ?></div>
</div>

</body>
</html>
