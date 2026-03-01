<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="shortcut icon" href="uzaylogo.ico">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>spacepedia.info</title>
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
<h3>Free Encyclopedia</h3>
<form method="post" action="add_encyclopedia_post.php">
    
<?php if(empty($_SESSION['csrf_token'])){$_SESSION['csrf_token']=bin2hex(random_bytes(32));}if(empty($_SESSION['form_time'])){$_SESSION['form_time']=time();} ?>
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
<input type="text" name="website" style="display:none" autocomplete="off">

<div class="form-group">
<label for="title">Title:</label>
<input type="text" class="form-control" id="title" name="title" placeholder="Enter the Title." required>
</div>  

<div class="form-group">
<label for="content">Content:</label>
<div class="editor-wrapper">
<div id="post-body" style="height: 300px;"></div>
</div>
<input type="hidden" name="content" id="hiddenContent">
</div>

<button type="submit" class="btn btn-success">Submit Encyclopedia</button>

</form>
</div>
</div>
</div>

<script>
var quill = new Quill('#post-body', {
modules: { 
    toolbar: [
        ['bold','italic','underline'],
        ['link','image'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }]
    ] 
},
theme: 'snow'
});

document.querySelector('form').onsubmit = function() {
    document.getElementById('hiddenContent').value = quill.root.innerHTML;
};
</script>

</body>
</html>
