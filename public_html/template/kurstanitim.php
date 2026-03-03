<?php
session_start();
require_once('../db_config.php');

$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if(!$course_id) die("Geçersiz kurs ID'si.");

try {
    $stmt = $db->prepare("SELECT * FROM course_posts WHERE id=:id");
    $stmt->bindParam(':id',$course_id);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$course) die("Kurs bulunamadı.");
} catch(PDOException $e){
    die("Veritabanı Hatası: ".$e->getMessage());
}
$video = $course['intro_video'];
$youtube_id = '';
if(strpos($video,'youtube.com') !== false || strpos($video,'youtu.be') !== false){
    preg_match('/(?:v=|\/)([a-zA-Z0-9_-]{11})/', $video, $matches);
    $youtube_id = $matches[1] ?? '';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($course['title']); ?> - Tanıtım Videosu</title>
<link rel="stylesheet" href="https://uzay.info/template/backgroundonly.css">
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; display:flex; justify-content:center; align-items:center; min-height:100vh; }
.container { max-width:800px; width:100%; padding:10px; }
.video-box iframe, .video-box video { width:100%; height:450px; }
h1 { text-align:center; margin-bottom:20px; color: #6600ff; }
</style>
</head>
<body>
<div class="container">
<h1><?php echo htmlspecialchars($course['title']); ?> - Tanıtım Videosu</h1>
<div class="video-box">
<?php
if($youtube_id){
    echo '<iframe src="https://www.youtube.com/embed/'.$youtube_id.'" frameborder="0" allowfullscreen></iframe>';
} elseif($video){
    echo '<video controls><source src="'.htmlspecialchars($video).'" type="video/mp4">Tarayıcınız video etiketini desteklemiyor.</video>';
} else {
    echo "<p>Tanıtım videosu bulunmamaktadır.</p>";
}
?>
</div>
</div>
</body>
</html>
