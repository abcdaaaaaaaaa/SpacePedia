<?php
session_start();
require_once('../db_config.php');

if (!isset($_GET['post_id'])) {
    header("Location: /forums");
    exit();
}

$post_id = intval($_GET['post_id']);

$stmt = $db->prepare("SELECT forum_posts2.*, users.username FROM forum_posts2 LEFT JOIN users ON forum_posts2.user_id = users.id WHERE forum_posts2.id = :pid");
$stmt->bindValue(':pid', $post_id, PDO::PARAM_INT);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    echo "Forum post not found.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $user_id = $_SESSION['user_id'];
    $message = $_POST['message'];
    $stmt = $db->prepare("INSERT INTO forum_messages2 (post_id, user_id, message, created_at) VALUES (:post_id, :user_id, :message, NOW())");
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':message', $message, PDO::PARAM_STR);
    $stmt->execute();
    header("Location: /forum_detail?post_id=$post_id");
    exit();
}

if (isset($_GET['delete_message_id'])) {
    $msg_id = intval($_GET['delete_message_id']);
    $stmt = $db->prepare("DELETE FROM forum_messages2 WHERE id = :mid AND user_id = :uid");
    $stmt->bindValue(':mid', $msg_id, PDO::PARAM_INT);
    $stmt->bindValue(':uid', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    header("Location: /forum_detail?post_id=$post_id");
    exit();
}

$stmt = $db->prepare("SELECT forum_messages2.*, users.username FROM forum_messages2 LEFT JOIN users ON forum_messages2.user_id = users.id WHERE forum_messages2.post_id = :pid ORDER BY forum_messages2.created_at ASC");
$stmt->bindValue(':pid', $post_id, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forum Details</title>
<link rel="stylesheet" href="https://uzay.info/template/backgroundonly.css">
<style>
body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
header { background-color: #072768; color: white; padding: 0.6em 1em; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; }
header a.logo { color: white; text-decoration: none; font-weight: bold; font-size: 18px; margin-right: 15px; }
.container { max-width: 800px; margin: auto; padding: 15px; }
.post-title { font-size: 22px; font-weight: bold; margin-bottom: 10px; }
.post-info { font-size: 12px; color: #555; margin-bottom: 20px; }
.message-box { border: 1px solid #ddd; padding: 10px; border-radius: 5px; margin-bottom: 10px; position: relative; background: #f9f9f9; }
.message-box p { margin: 5px 0; }
.message-actions { position: absolute; top: 5px; right: 10px; }
.message-actions a { font-size: 12px; margin-left: 5px; text-decoration: none; color: red; }
form textarea { width: 100%; height: 100px; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; resize: vertical; }
form button { padding: 8px 15px; border: none; border-radius: 5px; background: #ff9800; color: white; cursor: pointer; }
form button:hover { background: #e68900; }
</style>
</head>
<body>
<header>
<a href="/forums" class="logo">← All Forums</a>
<a style="font-weight:bold; font-size: 18px; color: turquoise;"><?php echo htmlspecialchars($post['title']); ?></a>
</header>

<div class="container">
<div class="post-title"><?php echo htmlspecialchars($post['title']); ?></div>
<div class="post-info">Created by <?php echo htmlspecialchars($post['username']); ?> | Date: <?php echo date("d-m-Y H:i", strtotime($post['created_at'])); ?></div>

<?php foreach($messages as $msg): ?>
<div class="message-box">
    <p><b><?php echo htmlspecialchars($msg['username']); ?>:</b></p>
    <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
    <p style="font-size: 10px; color: #777;">Date: <?php echo date("d-m-Y H:i", strtotime($msg['created_at'])); ?></p>
    <?php if ($_SESSION['user_id'] == $msg['user_id']): ?>
    <div class="message-actions">
        <a href="/editmessage?msg_id=<?php echo $msg['id']; ?>&post_id=<?php echo $post_id; ?>">Edit</a>
        <a href="?delete_message_id=<?php echo $msg['id']; ?>&post_id=<?php echo $post_id; ?>" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<h3>Add New Message</h3>
<form method="post" action="">
    <textarea name="message" required></textarea>
    <button type="submit">Submit</button>
</form>
</div>
</body>
</html>