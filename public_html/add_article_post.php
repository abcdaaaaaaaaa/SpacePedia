<?php
session_start();
function goHome(){header("Location: /");exit;}
if(!isset($_SESSION['user_id'])){goHome();}
if($_SERVER["REQUEST_METHOD"]!=="POST"){goHome();}
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

function failAndGoHome($coverTarget=null,$pdfTarget=null){
	if($coverTarget&&file_exists($coverTarget)){unlink($coverTarget);}
	if($pdfTarget&&file_exists($pdfTarget)){unlink($pdfTarget);}
	goHome();
}
function hasDangerousName($name){
	if($name===''||strlen($name)>255){return true;}
	if(preg_match('/[\x00-\x1F\x7F]/u',$name)){return true;}
	if(strpos($name,'..')!==false){return true;}
	if(preg_match('/[\/\\\\]/',$name)){return true;}
	return false;
}

$user_id=(int)$_SESSION['user_id'];
$title=trim($_POST['title']);
$summary=trim($_POST['summary']);
$visibility=isset($_POST['visibility'])&&(string)$_POST['visibility']==='0'?0:1;

$uploadDir=__DIR__.'/ebook/';
if(!is_dir($uploadDir)){
	if(!mkdir($uploadDir,0755,true)){goHome();}
}
if(!is_dir($uploadDir)||!is_writable($uploadDir)){goHome();}

$coverTmp=$_FILES['cover']['tmp_name'];
$coverOriginal=(string)$_FILES['cover']['name'];
$coverSize=(int)$_FILES['cover']['size'];
$coverExt=strtolower(pathinfo($coverOriginal,PATHINFO_EXTENSION));
$coverImageInfo=@getimagesize($coverTmp);
$coverAllowedExt=['jpg','jpeg','png','webp'];

if(hasDangerousName($coverOriginal)){goHome();}
if(!in_array($coverExt,$coverAllowedExt,true)){goHome();}
if($coverImageInfo===false){goHome();}
if($coverSize<=0||$coverSize>5*1024*1024){goHome();}

$pdfTmp=$_FILES['pdf']['tmp_name'];
$pdfOriginal=(string)$_FILES['pdf']['name'];
$pdfSize=(int)$_FILES['pdf']['size'];
$pdfExt=strtolower(pathinfo($pdfOriginal,PATHINFO_EXTENSION));

if(hasDangerousName($pdfOriginal)){goHome();}
if($pdfExt!=='pdf'){goHome();}
if($pdfSize<=0||$pdfSize>25*1024*1024){goHome();}

$pdfHead=file_get_contents($pdfTmp,false,null,0,5);
if($pdfHead===false||$pdfHead!=='%PDF-'){goHome();}

$cover_name='cover_'.$user_id.'_'.bin2hex(random_bytes(16)).'.'.$coverExt;
$pdf_name='pdf_'.$user_id.'_'.bin2hex(random_bytes(16)).'.pdf';

$coverTarget=$uploadDir.$cover_name;
$pdfTarget=$uploadDir.$pdf_name;

if(file_exists($coverTarget)||file_exists($pdfTarget)){goHome();}

if(!move_uploaded_file($coverTmp,$coverTarget)){goHome();}
if(!move_uploaded_file($pdfTmp,$pdfTarget)){failAndGoHome($coverTarget,null);}

$table=$lang==='en'?'ebook_posts2':'ebook_posts';

try{
	$db->beginTransaction();

	$stmt=$db->prepare("INSERT INTO {$table} (user_id,title,summary,cover,pdf,visibility) VALUES (:user_id,:title,:summary,:cover,:pdf,:visibility)");
	$stmt->bindParam(':user_id',$user_id,PDO::PARAM_INT);
	$stmt->bindParam(':title',$title,PDO::PARAM_STR);
	$stmt->bindParam(':summary',$summary,PDO::PARAM_STR);
	$stmt->bindParam(':cover',$cover_name,PDO::PARAM_STR);
	$stmt->bindParam(':pdf',$pdf_name,PDO::PARAM_STR);
	$stmt->bindParam(':visibility',$visibility,PDO::PARAM_INT);
	$stmt->execute();

	if($visibility===1){
		$db->commit();
		if($lang==='en'){
			header("Location: /ebooks");
		}else{
			header("Location: /ekitaplar");
		}
		exit();
	}

	$stmt2=$db->prepare("SELECT username FROM users WHERE id=:user_id LIMIT 1");
	$stmt2->bindParam(':user_id',$user_id,PDO::PARAM_INT);
	$stmt2->execute();
	$user=$stmt2->fetch(PDO::FETCH_ASSOC);

	if(!$user||empty($user['username'])){
		if($db->inTransaction()){$db->rollBack();}
		failAndGoHome($coverTarget,$pdfTarget);
	}

	$db->commit();

	$username=$user['username'];

	if($lang==='en'){
		header("Location: /@/".rawurlencode($username)."/ebooks");
	}else{
		header("Location: /@/".rawurlencode($username)."/ekitaplar");
	}
	exit();
}catch(Throwable $e){
	if($db->inTransaction()){$db->rollBack();}
	failAndGoHome($coverTarget,$pdfTarget);
}
?>
