<?php
session_start();
require_once('../db_config.php');

if (!isset($_GET['post_id'])) {
    header("Location: /forumlar");
    exit();
}

$post_id = intval($_GET['post_id']);

$stmt = $db->prepare("SELECT forum_posts.*, users.username FROM forum_posts LEFT JOIN users ON forum_posts.user_id = users.id WHERE forum_posts.id = :pid");
$stmt->bindValue(':pid', $post_id, PDO::PARAM_INT);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    echo "Forum postu bulunamadı.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $user_id = $_SESSION['user_id'];
    $message = $_POST['message'];
    $stmt = $db->prepare("INSERT INTO forum_messages (post_id, user_id, message, created_at) VALUES (:post_id, :user_id, :message, NOW())");
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':message', $message, PDO::PARAM_STR);
    $stmt->execute();
    header("Location: /forumdetay?post_id=$post_id");
    exit();
}

if (isset($_GET['delete_message_id'])) {
    $msg_id = intval($_GET['delete_message_id']);
    $stmt = $db->prepare("DELETE FROM forum_messages WHERE id = :mid AND user_id = :uid");
    $stmt->bindValue(':mid', $msg_id, PDO::PARAM_INT);
    $stmt->bindValue(':uid', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    header("Location: /forumdetay?post_id=$post_id");
    exit();
}

$stmt = $db->prepare("SELECT forum_messages.*, users.username FROM forum_messages LEFT JOIN users ON forum_messages.user_id = users.id WHERE forum_messages.post_id = :pid ORDER BY forum_messages.created_at ASC");
$stmt->bindValue(':pid', $post_id, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forum Detayları</title>
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
<a href="/forumlar" class="logo">← Tüm Forumlar</a>
<a style="font-weight:bold; font-size: 18px; color: turquoise;"><?php echo htmlspecialchars($post['title']); ?></a>
</header>

<div class="container">
<div class="post-title"><?php echo htmlspecialchars($post['title']); ?></div>
<div class="post-info">Oluşturan: <?php echo htmlspecialchars($post['username']); ?> | Tarih: <?php echo date("d-m-Y H:i", strtotime($post['created_at'])); ?></div>

<?php foreach($messages as $msg): ?>
<div class="message-box">
    <p><b><?php echo htmlspecialchars($msg['username']); ?>:</b></p>
    <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
    <p style="font-size: 10px; color: #777;">Tarih: <?php echo date("d-m-Y H:i", strtotime($msg['created_at'])); ?></p>
    <?php if ($_SESSION['user_id'] == $msg['user_id']): ?>
    <div class="message-actions">
        <a href="/editmessage?msg_id=<?php echo $msg['id']; ?>&post_id=<?php echo $post_id; ?>">Düzenle</a>
        <a href="?delete_message_id=<?php echo $msg['id']; ?>&post_id=<?php echo $post_id; ?>" onclick="return confirm('Mesajı silmek istediğinizden emin misiniz?');">Sil</a>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<h3>Yeni Mesaj Ekle</h3>
<form method="post" action="">
    <textarea name="message" required></textarea>
    <button type="submit">Gönder</button>
</form>
</div>
</body>
</html>