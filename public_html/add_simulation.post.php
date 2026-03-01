<?php
session_start();
function goHome(){ header("Location: /"); exit; }
if(!isset($_SESSION['user_id'])){ goHome(); }
if(!empty($_POST['website'])){ goHome(); }
if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')){ goHome(); }
if(!isset($_SESSION['form_time']) || time()-$_SESSION['form_time'] < 8){ goHome(); }
if(empty(trim($_POST['title'] ?? ''))){ goHome(); }
if(empty(trim($_POST['features'] ?? ''))){ goHome(); }
if(empty(trim($_POST['description'] ?? ''))){ goHome(); }
if(!isset($_POST['html_code']) || mb_strlen(trim(strip_tags($_POST['html_code']))) < 100){ goHome(); }
preg_match_all('/https?:\/\/|www\./i', $_POST['html_code'], $matches);
if(count($matches[0]) > 3){ goHome(); }
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
require_once('db_config.php');

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $features = $_POST['features'];
    $description = $_POST['description'];
    $html_code = $_POST['html_code'];
    $visibility = $_POST['visibility'];

    $table = ($lang === 'en') ? 'simulation_posts2' : 'simulation_posts';

    $stmt = $db->prepare("
        INSERT INTO {$table} 
        (user_id,title,features,description,html_code,visibility) 
        VALUES 
        (:user_id,:title,:features,:description,:html_code,:visibility)
    ");
    $stmt->bindParam(':user_id',$user_id);
    $stmt->bindParam(':title',$title);
    $stmt->bindParam(':features',$features);
    $stmt->bindParam(':description',$description);
    $stmt->bindParam(':html_code',$html_code);
    $stmt->bindParam(':visibility',$visibility);
    $stmt->execute();

    $stmt2 = $db->prepare("SELECT username FROM users WHERE id=:user_id");
    $stmt2->bindParam(':user_id',$user_id);
    $stmt2->execute();
    $user = $stmt2->fetch(PDO::FETCH_ASSOC);
    $username = $user['username'];

    if($visibility == 1){
        header("Location: " . ($lang==='en'? '/simulations' : '/simulasyonlar'));
    } else {
        header("Location: /@/$username/" . ($lang==='en'? 'simulations' : 'simulasyonlar'));
    }
    exit();
}
?>
