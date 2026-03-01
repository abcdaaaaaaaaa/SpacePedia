<?php
session_start();
require_once('../db_config.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) die("Geçersiz ansiklopedi ID");

try {
    $stmt = $db->prepare("SELECT * FROM encyclopedia_posts WHERE id=:id");
    $stmt->execute([':id'=>$id]);
    $encyclopedia = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$encyclopedia) die("Özgür Ansiklopedi bulunamadı.");
} catch(PDOException $e){
    die("Veritabanı Hatası: ".$e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title']);
    $content = $_POST['content'];
    if ($title === '' || $content === '') {
        $error = "Başlık ve içerik boş olamaz.";
    } else {
        try {
            $stmt = $db->prepare("UPDATE encyclopedia_posts 
                                  SET title=:title, content=:content, updated_at=NOW() 
                                  WHERE id=:id");
            $stmt->execute([
                ':title'   => $title,
                ':content' => $content,
                ':id'      => $id
            ]);
            header("Location: /ozguransiklopedidetay?id=".$id);
            exit;
        } catch(PDOException $e){
            $error = "Güncelleme hatası: ".$e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Düzenle - <?php echo htmlspecialchars($encyclopedia['title']); ?></title>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; background:#ecebe8; }
.container { max-width:800px; margin:30px auto; background:white; padding:20px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
h1 { margin-bottom:20px; color:#ff9800; }
input[type="text"] { width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px; font-size:14px; }
.editor-wrapper { background:white; border:1px solid #ccc; border-radius:5px; }
#post-body { height:300px; }
.buttons { display:flex; justify-content:space-between; margin-top:15px; }
button, .back-btn { background:#4CAF50; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer; font-weight:bold; text-decoration:none; text-align:center; }
button:hover { background:#3b8c40; }
.back-btn { background:#607d8b; }
.back-btn:hover { background:#455a64; }
.error { color:red; margin-bottom:10px; }
</style>
</head>
<body>
<div class="container">
    <h1>Özgür Ansiklopediyi Düzenle</h1>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
        <label>Başlık</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($encyclopedia['title']); ?>">
        <label>İçerik</label>
        <div class="editor-wrapper">
            <div id="post-body"></div>
        </div>
        <input type="hidden" name="content" id="hiddenContent">
        <div class="buttons">
            <a href="/ozguransiklopedidetay?id=<?php echo $id; ?>" class="back-btn">Geri Dön</a>
            <button type="submit">Kaydet</button>
        </div>
    </form>
</div>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
var quill = new Quill('#post-body', {
    modules: { toolbar: [['bold','italic','underline'],['link','image'],[{ 'list': 'ordered'}, { 'list': 'bullet' }]] },
    theme: 'snow'
});
quill.root.innerHTML = <?php echo json_encode($encyclopedia['content']); ?>;
document.querySelector('form').onsubmit = function() {
    document.getElementById('hiddenContent').value = quill.root.innerHTML;
};
</script>
</body>
</html>
