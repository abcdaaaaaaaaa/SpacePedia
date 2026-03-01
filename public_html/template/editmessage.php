<?php
session_start();
require_once('../db_config.php');

if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}

$table=$lang==='tr'?'forum_messages':'forum_messages2';

if(!isset($_GET['msg_id'])||!isset($_GET['post_id'])){
    header("Location: /forums");
    exit();
}

$msg_id=(int)$_GET['msg_id'];
$post_id=(int)$_GET['post_id'];

$stmt=$db->prepare("SELECT * FROM {$table} WHERE id=:mid AND user_id=:uid");
$stmt->bindValue(':mid',$msg_id,PDO::PARAM_INT);
$stmt->bindValue(':uid',$_SESSION['user_id'],PDO::PARAM_INT);
$stmt->execute();
$message=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$message){
    echo $lang==='tr'?'Bu mesaj size ait değil veya bulunamadı.':'This message does not belong to you or was not found.';
    exit();
}

if($_SERVER["REQUEST_METHOD"]==="POST"){
    $newMessage=trim($_POST['message']??'');
    $stmt=$db->prepare("UPDATE {$table} SET message=:msg WHERE id=:mid AND user_id=:uid");
    $stmt->bindValue(':msg',$newMessage,PDO::PARAM_STR);
    $stmt->bindValue(':mid',$msg_id,PDO::PARAM_INT);
    $stmt->bindValue(':uid',$_SESSION['user_id'],PDO::PARAM_INT);
    $stmt->execute();
    header("Location: /forum_detail?post_id=".$post_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang==='tr'?'tr':'en'; ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $lang==='tr'?'Mesaj Düzenle':'Edit Message'; ?></title>
<link rel="stylesheet" href="https://uzay.info/template/backgroundonly.css">
<style>
body{font-family:Arial,sans-serif;margin:0;padding:0}
.container{max-width:600px;margin:40px auto;padding:20px;border:1px solid #ddd;border-radius:5px;background:#fff}
h2{margin-top:0}
textarea{width:100%;height:150px;padding:10px;border-radius:5px;border:1px solid #ccc;resize:vertical;margin-bottom:10px}
button{padding:8px 15px;border:none;border-radius:5px;background:#ff9800;color:#fff;cursor:pointer}
button:hover{background:#e68900}
a{display:inline-block;margin-top:10px;text-decoration:none;color:#072768}
</style>
</head>
<body>
<div class="container">
<h2><?php echo $lang==='tr'?'Mesajını Düzenle':'Edit Your Message'; ?></h2>
<form method="post" action="">
    <textarea name="message" required><?php echo htmlspecialchars($message['message']); ?></textarea>
    <button type="submit"><?php echo $lang==='tr'?'Kaydet':'Save'; ?></button>
</form>
<a href="/forum_detail?post_id=<?php echo $post_id; ?>">← <?php echo $lang==='tr'?'Geri Dön':'Go Back'; ?></a>
</div>
</body>
</html>