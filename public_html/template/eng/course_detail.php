<?php
session_start();
require_once('../db_config.php');

$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if(!$course_id) die("Invalid course ID'si.");

try {
    $stmt = $db->prepare("SELECT * FROM course_posts2 WHERE id=:id");
    $stmt->bindParam(':id',$course_id);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$course) die("Course not found");
} catch(PDOException $e){
    die("Database Error: ".$e->getMessage());
}

$videos = [];
if (!empty($course['videos'])) {
    $videos = explode(";", $course['videos']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($course['title']); ?> -  Course Sections</title>
<link rel="stylesheet" href="https://uzay.info/template/backgroundonly.css">
<style>
body { font-family: Arial, sans-serif; margin:0; padding:20px; }
.container { max-width:800px; margin:auto; background:white; padding:20px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
h1 { text-align:center; margin-bottom:20px; color: #6600ff; }
.video-box { display:none; text-align:center; }
.video-box.active { display:block; }
.video-box iframe, .video-box video { width:100%; height:400px; margin-top:10px; }
.controls { display:flex; justify-content:space-between; margin-top:20px; }
.controls button { padding:10px 15px; background:#072768; color:white; border:none; border-radius:5px; cursor:pointer; }
.controls button:hover { background:#0a3a9d; }
.hidden { display:none; }
</style>
</head>
<body>
<div class="container">
    <h1><?php echo htmlspecialchars($course['title']); ?> - Course Sections</h1>

    <?php if (!empty($videos)): ?>
        <?php foreach ($videos as $index => $video): ?>
            <div class="video-box <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                <h3>Section <?php echo $index+1; ?></h3>
                <?php
                $youtube_id = '';
                if(strpos($video,'youtube.com') !== false || strpos($video,'youtu.be') !== false){
                    preg_match('/(?:v=|\/)([a-zA-Z0-9_-]{11})/', $video, $matches);
                    $youtube_id = $matches[1] ?? '';
                }
                if ($youtube_id) {
                    echo '<iframe src="https://www.youtube.com/embed/'.$youtube_id.'" frameborder="0" allowfullscreen></iframe>';
                } elseif (!empty($video)) {
                    echo '<video controls><source src="'.htmlspecialchars($video).'" type="video/mp4">Your browser does not support the video tag.</video>';
                } else {
                    echo "<p>Video unavailable.</p>";
                }
                ?>
            </div>
        <?php endforeach; ?>

        <div class="controls">
            <button id="prevBtn" class="hidden">← Previous</button>
            <button id="nextBtn">Next →</button>
        </div>

    <?php else: ?>
        <p>This course has no section videos.</p>
    <?php endif; ?>
</div>

<script>
const videos = document.querySelectorAll('.video-box');
let currentIndex = 0;
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');

function updateButtons() {
    prevBtn.classList.toggle('hidden', currentIndex === 0);
    nextBtn.classList.toggle('hidden', currentIndex === videos.length - 1);
}

function showVideo(index) {
    videos.forEach(v => v.classList.remove('active'));
    videos[index].classList.add('active');
    updateButtons();
}

nextBtn.addEventListener('click', () => {
    if (currentIndex < videos.length - 1) {
        currentIndex++;
        showVideo(currentIndex);
    }
});

prevBtn.addEventListener('click', () => {
    if (currentIndex > 0) {
        currentIndex--;
        showVideo(currentIndex);
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === "ArrowRight" && currentIndex < videos.length - 1) {
        currentIndex++;
        showVideo(currentIndex);
    } else if (e.key === "ArrowLeft" && currentIndex > 0) {
        currentIndex--;
        showVideo(currentIndex);
    }
});

updateButtons();
</script>
</body>
</html>
