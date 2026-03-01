<?php
session_start();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<link rel="shortcut icon" href="uzaylogo.ico">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>uzay.info</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
<link rel="stylesheet" href="https://uzay.info/template/backgroundonly.css">
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-lg-offset-2 col-lg-8" style="margin-top:40px;">
            <h3>e-kitap</h3>
            <form method="post" action="add_ebook_post.php" enctype="multipart/form-data">
                
                <?php if(empty($_SESSION['csrf_token'])){$_SESSION['csrf_token']=bin2hex(random_bytes(32));}if(empty($_SESSION['form_time'])){$_SESSION['form_time']=time();} ?>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" name="website" style="display:none" autocomplete="off">

                <div class="form-group">
                    <label for="cover">e-kitap Kapak Resmi</label>
                    <input class="file-upload-input" id="cover" name="cover" type="file" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="pdf">e-kitap PDF Dosyası</label>
                    <input class="file-upload-input" id="pdf" name="pdf" type="file" accept=".pdf">
                </div>

                <div class="form-group">
                    <label for="title">Başlık:</label>
                    <input type="text" class="form-control" id="title" name="title">
                </div>  
                

                <div class="form-group">
                    <label for="summary">e-kitap Özeti:</label>
                    <textarea class="form-control" name="summary" id="summary"></textarea>
                </div>		
                
                <div class="form-group">
                    <label for="visibility">e-kitap Görünürlüğü</label>
                    <select id="visibility" name="visibility" class="form-control">
                        <option value="1" selected>Tüm e-kitaplar kısmında görünsün.</option>
                        <option value="2">Sadece profilde görünmesini istiyorum.</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Gönder</button>

            </form>
        </div>
    </div>
</div>

</body>
</html>