<?php
session_start();
if(!isset($_SESSION['username'])){
header("Location:/login");
exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<link rel="shortcut icon" href="uzaylogo.ico">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>uzay.info</title>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://uzay.info/template/backgroundonly.css">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<style>
.editor-wrapper { 
    background-color: white; 
    padding: 20px; 
}
</style>
</head>
<body>

<div class="container">
<div class="row">
<div class="col-lg-offset-2 col-lg-8" style="margin-top:40px;">
<h3>Öğretici Makale</h3>
<form method="post" action="add_blog_post.php" enctype="multipart/form-data">
    
<?php if(empty($_SESSION['csrf_token'])){$_SESSION['csrf_token']=bin2hex(random_bytes(32));}if(empty($_SESSION['form_time'])){$_SESSION['form_time']=time();} ?>
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
<input type="text" name="website" style="display:none" autocomplete="off">

<div class="form-group">
<label for="cover">Öğretici Makale Kapak Resmi</label>
<input class="file-upload-input" name="cover" id="cover" type="file" accept="image/*">
</div>

<div class="form-group">
<label for="title">Başlık:</label>
<input type="text" class="form-control" id="title" name="title" placeholder="Başlık yazınız.">
</div>  

<div class="form-group">
<label for="theme">Tema</label>
<select id="theme" name="theme" class="form-control">
<option value="13">Kara Delikler</option>
<option value="12">Galaksiler</option>
<option value="11">Nötron Yıldızları</option>
<option value="10">Kuyruklu Yıldızlar</option>
<option value="9">Takım Yıldızları</option>
<option value="8">Yıldızlar</option>
<option value="7">Gezegenler</option>
<option value="6">Bulutsular</option>
<option value="5">Genel Uzay</option>
<option value="4">Kazalar</option>
<option value="3">Farklı Yöntemler</option>
<option value="2" selected>Uzay Araçları</option>
<option value="1">Yeni Gelişmeler</option>
</select>
</div>

<div class="form-group">
<label for="subject">Konu</label>
<input type="text" class="form-control" id="subject" name="subject" placeholder="Konu yazınız.">
</div>         

<div class="form-group">
<label for="summary">Makale Özeti</label>
<textarea class="form-control" id="summary" name="summary" rows="3"></textarea>
</div>

<div class="form-group">
<label for="content">Makale İçeriği:</label>
<div class="editor-wrapper">
<div id="post-body" style="height: 300px;"></div>
</div>
<input type="hidden" name="content" id="hiddenContent">
</div>

<div class="form-group">
<label for="visibility">Makale Görünürlüğü</label>
<select id="visibility" name="visibility" class="form-control">
<option value="1" selected>Tüm Öğretici Makaleler kısmında görünsün.</option>
<option value="2">Sadece profilde görünmesini istiyorum.</option>
</select>
</div>

<button type="submit" class="btn btn-primary">Öğretici Makaleyi Gönder</button>

</form>
</div>
</div>
</div>

<script>
var quill = new Quill('#post-body', {
modules: { toolbar: [['bold','italic','underline'],['link','image'],[{ 'list': 'ordered'}, { 'list': 'bullet' }]] },
theme: 'snow'
});

document.querySelector('form').onsubmit = function() {
    document.getElementById('hiddenContent').value = quill.root.innerHTML;
};
</script>

</body>
</html>
