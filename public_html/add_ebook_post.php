<?php
session_start();
function goHome(){header("Location: /");exit;}
if(!isset($_SESSION['user_id'])){goHome();}
if(!empty($_POST['website'])){goHome();}
if(!isset($_POST['csrf_token'])||$_POST['csrf_token']!==($_SESSION['csrf_token']??'')){goHome();}
if(!isset($_SESSION['form_time'])||time()-$_SESSION['form_time']<8){goHome();}
if(empty(trim($_POST['title']??''))){goHome();}
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
	$summary = $_POST['summary'];
	$visibility = $_POST['visibility'];

	$cover_name = null;
	$pdf_name = null;

	if(isset($_FILES['cover']) && $_FILES['cover']['error']===UPLOAD_ERR_OK){
		$cover_name = time().'_'.basename($_FILES['cover']['name']);
		move_uploaded_file($_FILES['cover']['tmp_name'],'ebook/'.$cover_name);
	}

	if(isset($_FILES['pdf']) && $_FILES['pdf']['error']===UPLOAD_ERR_OK){
		$pdf_name = time().'_'.basename($_FILES['pdf']['name']);
		move_uploaded_file($_FILES['pdf']['tmp_name'],'ebook/'.$pdf_name);
	}

	$table = ($lang === 'en') ? 'ebook_posts2' : 'ebook_posts';

	$stmt = $db->prepare("
		INSERT INTO {$table}
		(user_id,title,summary,cover,pdf,visibility)
		VALUES
		(:user_id,:title,:summary,:cover,:pdf,:visibility)
	");
	$stmt->bindParam(':user_id',$user_id);
	$stmt->bindParam(':title',$title);
	$stmt->bindParam(':summary',$summary);
	$stmt->bindParam(':cover',$cover_name);
	$stmt->bindParam(':pdf',$pdf_name);
	$stmt->bindParam(':visibility',$visibility);
	$stmt->execute();

	if($visibility == 1){
		if($lang === 'en'){
			header("Location: /ebooks");
		}else{
			header("Location: /ekitaplar");
		}
	}else{
		$stmt2 = $db->prepare("SELECT username FROM users WHERE id=:user_id");
		$stmt2->bindParam(':user_id',$user_id);
		$stmt2->execute();
		$user = $stmt2->fetch(PDO::FETCH_ASSOC);
		$username = $user['username'];

		if($lang === 'en'){
			header("Location: /@/$username/ebooks");
		}else{
			header("Location: /@/$username/ekitaplar");
		}
	}
	exit();
}
?>
