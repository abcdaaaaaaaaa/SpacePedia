<?php
session_start();
function goHome(){header("Location: /");exit;}
if(!isset($_SESSION['user_id'])){goHome();}
if(!empty($_POST['website'])){goHome();}
if(!isset($_POST['csrf_token'])||$_POST['csrf_token']!==($_SESSION['csrf_token']??'')){goHome();}
if(!isset($_SESSION['form_time'])||time()-$_SESSION['form_time']<8){goHome();}
if(empty(trim($_POST['title']??''))){goHome();}
if(empty(trim($_POST['subject']??''))){goHome();}
if(empty(trim($_POST['summary']??''))){goHome();}
if(empty(trim($_POST['purpose']??''))){goHome();}
if(empty(trim($_POST['audience']??''))){goHome();}
if(!isset($_POST['summary'])||mb_strlen(trim(strip_tags($_POST['summary'])))<100){goHome();}
if(!isset($_FILES['cover'])||!is_array($_FILES['cover'])||($_FILES['cover']['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK||empty($_FILES['cover']['tmp_name'])||!is_uploaded_file($_FILES['cover']['tmp_name'])||empty($_FILES['cover']['name'])){goHome();}
if(!isset($_FILES['pdf'])||!is_array($_FILES['pdf'])||($_FILES['pdf']['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK||empty($_FILES['pdf']['tmp_name'])||!is_uploaded_file($_FILES['pdf']['tmp_name'])||empty($_FILES['pdf']['name'])){goHome();}
preg_match_all('/https?:\/\/|www\./i',$_POST['summary'],$matches);
if(count($matches[0])>3){goHome();}
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
require_once('db_config.php');

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $subject = $_POST['subject'];
    $summary = $_POST['summary'];
    $purpose = $_POST['purpose'];
    $audience = $_POST['audience'];
    $visibility = $_POST['visibility'];

    $cover_name = null;
    $pdf_name = null;

    if(isset($_FILES['cover']) && $_FILES['cover']['error']===UPLOAD_ERR_OK){
        $cover_name = time().'_'.basename($_FILES['cover']['name']);
        move_uploaded_file($_FILES['cover']['tmp_name'],'article/'.$cover_name);
    }

    if(isset($_FILES['pdf']) && $_FILES['pdf']['error']===UPLOAD_ERR_OK){
        $pdf_name = time().'_'.basename($_FILES['pdf']['name']);
        move_uploaded_file($_FILES['pdf']['tmp_name'],'article/'.$pdf_name);
    }

    $table = ($lang === 'en') ? 'article_posts2' : 'article_posts';

    $stmt = $db->prepare("INSERT INTO {$table} (user_id,title,subject,summary,purpose,audience,cover,pdf,visibility) VALUES (:user_id,:title,:subject,:summary,:purpose,:audience,:cover,:pdf,:visibility)");
    $stmt->bindParam(':user_id',$user_id);
    $stmt->bindParam(':title',$title);
    $stmt->bindParam(':subject',$subject);
    $stmt->bindParam(':summary',$summary);
    $stmt->bindParam(':purpose',$purpose);
    $stmt->bindParam(':audience',$audience);
    $stmt->bindParam(':cover',$cover_name);
    $stmt->bindParam(':pdf',$pdf_name);
    $stmt->bindParam(':visibility',$visibility);
    $stmt->execute();

    $stmt2 = $db->prepare("SELECT username FROM users WHERE id=:user_id");
    $stmt2->bindParam(':user_id',$user_id);
    $stmt2->execute();
    $user = $stmt2->fetch(PDO::FETCH_ASSOC);
    $username = $user['username'];

    if($lang === 'en'){
        if($visibility == 1){ header("Location: /academic_articles"); }
        else { header("Location: /@/$username/academic_articles"); }
    } else {
        if($visibility == 1){ header("Location: /akademikmakaleler"); }
        else { header("Location: /@/$username/akademikmakaleler"); }
    }
    exit();
}
?>
