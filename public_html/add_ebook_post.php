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

function failAndGoHome(){goHome();}
function hasDangerousName($name){
	if($name===''||strlen($name)>255){return true;}
	if(preg_match('/[\x00-\x1F\x7F]/u',$name)){return true;}
	if(strpos($name,'..')!==false){return true;}
	if(preg_match('/[\/\\\\]/',$name)){return true;}
	if(substr_count($name,'.')<1){return true;}
	return false;
}
function getMime($tmp){
	$f=finfo_open(FILEINFO_MIME_TYPE);
	$m=finfo_file($f,$tmp);
	finfo_close($f);
	return $m;
}

if($_SERVER["REQUEST_METHOD"]==="POST"){
	$user_id=(int)$_SESSION['user_id'];
	$title=trim($_POST['title']);
	$summary=trim($_POST['summary']);
	$visibility=isset($_POST['visibility'])&&(string)$_POST['visibility']==='0'?0:1;

	$uploadDir=__DIR__.'/ebook/';
	if(!is_dir($uploadDir)){
		if(!mkdir($uploadDir,0755,true)){failAndGoHome();}
	}
	if(!is_dir($uploadDir)||!is_writable($uploadDir)){failAndGoHome();}

	$coverTmp=$_FILES['cover']['tmp_name'];
	$coverOriginal=(string)$_FILES['cover']['name'];
	$coverSize=(int)$_FILES['cover']['size'];
	$coverExt=strtolower(pathinfo($coverOriginal,PATHINFO_EXTENSION));
	$coverBase=pathinfo($coverOriginal,PATHINFO_FILENAME);
	$coverMime=getMime($coverTmp);
	$coverImageInfo=@getimagesize($coverTmp);
	$coverAllowedExt=['jpg','jpeg','png','webp'];
	$coverAllowedMime=['image/jpeg','image/png','image/webp'];

	if(hasDangerousName($coverOriginal)){failAndGoHome();}
	if($coverBase===''||preg_match('/\.(php|phtml|php3|php4|php5|php7|php8|phar|cgi|pl|py|jsp|asp|aspx|sh|exe|js|html|htm)$/i',$coverBase)){failAndGoHome();}
	if(!in_array($coverExt,$coverAllowedExt,true)){failAndGoHome();}
	if(!in_array($coverMime,$coverAllowedMime,true)){failAndGoHome();}
	if($coverImageInfo===false||!isset($coverImageInfo[0],$coverImageInfo[1])||$coverImageInfo[0]<1||$coverImageInfo[1]<1){failAndGoHome();}
	if($coverSize<=0||$coverSize>5*1024*1024){failAndGoHome();}

	$realImageType=$coverImageInfo[2]??null;
	$allowedImageTypes=[IMAGETYPE_JPEG,IMAGETYPE_PNG,IMAGETYPE_WEBP];
	if(!in_array($realImageType,$allowedImageTypes,true)){failAndGoHome();}
	if(($coverExt==='jpg'||$coverExt==='jpeg')&&$realImageType!==IMAGETYPE_JPEG){failAndGoHome();}
	if($coverExt==='png'&&$realImageType!==IMAGETYPE_PNG){failAndGoHome();}
	if($coverExt==='webp'&&$realImageType!==IMAGETYPE_WEBP){failAndGoHome();}

	$coverContent=file_get_contents($coverTmp,false,null,0,32);
	if($coverContent===false||$coverContent===''){failAndGoHome();}
	$coverStartsWithPhp=preg_match('/^\s*<\?(php|=)?/i',$coverContent);
	if($coverStartsWithPhp){failAndGoHome();}

	$pdfTmp=$_FILES['pdf']['tmp_name'];
	$pdfOriginal=(string)$_FILES['pdf']['name'];
	$pdfSize=(int)$_FILES['pdf']['size'];
	$pdfExt=strtolower(pathinfo($pdfOriginal,PATHINFO_EXTENSION));
	$pdfBase=pathinfo($pdfOriginal,PATHINFO_FILENAME);
	$pdfMime=getMime($pdfTmp);

	if(hasDangerousName($pdfOriginal)){failAndGoHome();}
	if($pdfBase===''||preg_match('/\.(php|phtml|php3|php4|php5|php7|php8|phar|cgi|pl|py|jsp|asp|aspx|sh|exe|js|html|htm)$/i',$pdfBase)){failAndGoHome();}
	if($pdfExt!=='pdf'){failAndGoHome();}
	if(!in_array($pdfMime,['application/pdf','application/x-pdf'],true)){failAndGoHome();}
	if($pdfSize<=0||$pdfSize>25*1024*1024){failAndGoHome();}

	$pdfHead=file_get_contents($pdfTmp,false,null,0,8);
	if($pdfHead===false||strncmp($pdfHead,'%PDF-',5)!==0){failAndGoHome();}

	$pdfTailSize=min($pdfSize,2048);
	$pdfHandle=fopen($pdfTmp,'rb');
	if($pdfHandle===false){failAndGoHome();}
	if(fseek($pdfHandle,-$pdfTailSize,SEEK_END)!==0&&$pdfSize>$pdfTailSize){fclose($pdfHandle);failAndGoHome();}
	$pdfTail=fread($pdfHandle,$pdfTailSize);
	fclose($pdfHandle);
	if($pdfTail===false||stripos($pdfTail,'%%EOF')===false){failAndGoHome();}

	$pdfStartChunk=file_get_contents($pdfTmp,false,null,0,4096);
	if($pdfStartChunk===false||$pdfStartChunk===''){failAndGoHome();}
	if(preg_match('/<\?(php|=)?/i',$pdfStartChunk)){failAndGoHome();}

	$cover_name='cover_'.$user_id.'_'.bin2hex(random_bytes(16)).'.'.$coverExt;
	$pdf_name='pdf_'.$user_id.'_'.bin2hex(random_bytes(16)).'.pdf';

	$coverTarget=$uploadDir.$cover_name;
	$pdfTarget=$uploadDir.$pdf_name;

	if(file_exists($coverTarget)||file_exists($pdfTarget)){failAndGoHome();}

	if(!move_uploaded_file($coverTmp,$coverTarget)){failAndGoHome();}
	if(!move_uploaded_file($pdfTmp,$pdfTarget)){
		if(file_exists($coverTarget)){unlink($coverTarget);}
		failAndGoHome();
	}

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
			$db->rollBack();
			if(file_exists($coverTarget)){unlink($coverTarget);}
			if(file_exists($pdfTarget)){unlink($pdfTarget);}
			failAndGoHome();
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
		if(file_exists($coverTarget)){unlink($coverTarget);}
		if(file_exists($pdfTarget)){unlink($pdfTarget);}
		failAndGoHome();
	}
}
?>
