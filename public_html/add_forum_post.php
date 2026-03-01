<?php
session_start();
function goHome(){header("Location: /");exit;}
if(!isset($_SESSION['user_id'])){goHome();}
if(!empty($_POST['website'])){goHome();}
if(!isset($_POST['csrf_token'])||$_POST['csrf_token']!==($_SESSION['csrf_token']??'')){goHome();}
if(!isset($_SESSION['form_time'])||time()-$_SESSION['form_time']<8){goHome();}
if(empty(trim($_POST['title']??''))){goHome();}
if(!isset($_POST['theme'])||!is_numeric($_POST['theme'])){goHome();}
if(!isset($_POST['message'])||mb_strlen(trim(strip_tags($_POST['message'])))<4){goHome();}
preg_match_all('/https?:\/\/|www\./i',$_POST['message'],$matches);
if(count($matches[0])>3){goHome();}
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
require_once('db_config.php');

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $theme = intval($_POST['theme']);
    $message = trim($_POST['message']);

    $post_table = ($lang === 'en') ? 'forum_posts2' : 'forum_posts';
    $message_table = ($lang === 'en') ? 'forum_messages2' : 'forum_messages';

    $stmt = $db->prepare("
        INSERT INTO {$post_table} 
        (user_id,title,theme,created_at) 
        VALUES 
        (:user_id,:title,:theme,NOW())
    ");
    $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
    $stmt->bindValue(':title',$title,PDO::PARAM_STR);
    $stmt->bindValue(':theme',$theme,PDO::PARAM_INT);
    $stmt->execute();
    $post_id = $db->lastInsertId();

    $stmt2 = $db->prepare("
        INSERT INTO {$message_table} 
        (post_id,user_id,message,created_at) 
        VALUES 
        (:post_id,:user_id,:message,NOW())
    ");
    $stmt2->execute([
        ':post_id' => $post_id,
        ':user_id' => $user_id,
        ':message' => $message
    ]);

    $stmt3 = $db->prepare("SELECT username FROM users WHERE id = :user_id");
    $stmt3->bindParam(':user_id',$user_id);
    $stmt3->execute();
    $user = $stmt3->fetch(PDO::FETCH_ASSOC);
    $username = $user['username'];

    if($lang === 'en'){
        header("Location: /forums");
    }else{
        header("Location: /forumlar");
    }
    exit();
}
?>
