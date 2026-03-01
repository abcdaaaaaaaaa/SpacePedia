<?php
session_start();
function goHome(){header("Location: /");exit;}
if(!empty($_POST['website'])){goHome();}
if(!isset($_POST['csrf_token'])||$_POST['csrf_token']!==($_SESSION['csrf_token']??'')){goHome();}
if(!isset($_SESSION['form_time'])||time()-$_SESSION['form_time']<8){goHome();}
if(empty(trim($_POST['title']??''))){goHome();}
if(!isset($_POST['content'])||mb_strlen(trim(strip_tags($_POST['content'])))<100){goHome();}
preg_match_all('/https?:\/\/|www\./i',$_POST['content'],$matches);
if(count($matches[0])>3){goHome();}
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
require_once('db_config.php');

if($_SERVER["REQUEST_METHOD"]=="POST"){
	$title = $_POST['title'] ?? '';
	$content = $_POST['content'] ?? '';

	if(!empty($title) && !empty($content)){
		$table = ($lang === 'en') ? 'encyclopedia_posts2' : 'encyclopedia_posts';

		$stmt = $db->prepare("
			INSERT INTO {$table}
			(title,content,created_at,updated_at)
			VALUES
			(:title,:content,NOW(),NOW())
		");
		$stmt->bindParam(':title',$title);
		$stmt->bindParam(':content',$content);
		$stmt->execute();
	}

	if($lang === 'en'){
		header("Location: /free_encyclopedias");
	}else{
		header("Location: /ozguransiklopediler");
	}
	exit();
}
?>
