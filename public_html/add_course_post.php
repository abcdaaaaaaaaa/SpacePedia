<?php
session_start();
function goHome(){header("Location: /");exit;}
if(!isset($_SESSION['user_id'])){goHome();}
if(!empty($_POST['website'])){goHome();}
if(!isset($_POST['csrf_token'])||$_POST['csrf_token']!==($_SESSION['csrf_token']??'')){goHome();}
if(!isset($_SESSION['form_time'])||time()-$_SESSION['form_time']<8){goHome();}
if(empty(trim($_POST['title']??''))){goHome();}
if(empty(trim($_POST['audience']??''))){goHome();}
if(empty(trim($_POST['intro_video']??''))){goHome();}
if(!isset($_POST['section_count'])||!is_numeric($_POST['section_count'])||(int)$_POST['section_count']<1){goHome();}
if(!isset($_POST['description'])||mb_strlen(trim(strip_tags($_POST['description'])))<60){goHome();}
preg_match_all('/https?:\/\/|www\./i',$_POST['description'],$matches);
if(count($matches[0])>3){goHome();}
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
require_once('db_config.php');

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $audience = $_POST['audience'];
    $section_count = intval($_POST['section_count']);
    $intro_video = $_POST['intro_video'];

    $videos = isset($_POST['videos']) ? $_POST['videos'] : [];
    $videos_str = implode(";", $videos);

    $table = ($lang === 'en') ? 'course_posts2' : 'course_posts';

    $stmt = $db->prepare("
        INSERT INTO {$table}
        (user_id,title,description,audience,intro_video,section_count,videos)
        VALUES
        (:user_id,:title,:description,:audience,:intro_video,:section_count,:videos)
    ");
    $stmt->bindParam(':user_id',$user_id);
    $stmt->bindParam(':title',$title);
    $stmt->bindParam(':description',$description);
    $stmt->bindParam(':audience',$audience);
    $stmt->bindParam(':intro_video',$intro_video);
    $stmt->bindParam(':section_count',$section_count);
    $stmt->bindParam(':videos',$videos_str);
    $stmt->execute();

    if($lang === 'en'){
        header("Location: /courses");
    } else {
        header("Location: /kurslar");
    }
    exit();
}
?>
