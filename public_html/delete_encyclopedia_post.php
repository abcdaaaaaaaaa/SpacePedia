<?php
session_start();

if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}

$L=[
'tr'=>[
'must_login'=>'Lütfen giriş yapmalısınız.',
'not_authorized'=>'Bu işlemi yapmanız için yetkiniz olmalı.',
'bad_id'=>'Geçersiz içerik.',
],
'en'=>[
'must_login'=>'You must be logged in.',
'not_authorized'=>'You must be authorized to perform this action.',
'bad_id'=>'Invalid content.',
],
];

if(!isset($L[$lang])){$lang='tr';}

if(!isset($_SESSION['user_id'])){
    echo $L[$lang]['must_login'];
    exit;
}

require_once('db_config.php');

$stmt=$db->prepare("SELECT verified FROM users WHERE id=:uid LIMIT 1");
$stmt->bindValue(':uid',(int)$_SESSION['user_id'],PDO::PARAM_INT);
$stmt->execute();
$user=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$user || (int)$user['verified']!==1){
    echo $L[$lang]['not_authorized'];
    exit;
}

$id=isset($_GET['id'])?(int)$_GET['id']:0;
if($id<1){
    echo $L[$lang]['bad_id'];
    exit;
}

$table=($lang==='en')?'encyclopedia_posts2':'encyclopedia_posts';

$stmt=$db->prepare("DELETE FROM {$table} WHERE id=:id");
$stmt->bindValue(':id',$id,PDO::PARAM_INT);
$stmt->execute();

if($lang==='en'){header("Location: /free_encyclopedias");}
else{header("Location: /ozguransiklopediler");}
exit;
?>