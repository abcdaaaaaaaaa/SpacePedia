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
<style>
    .row {
        display: flex;
        align-items: stretch;
    }
    .col-lg-6 {
        display: flex;
        flex-direction: column;
    }
    form {
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    form button {
        margin-top: auto;
        align-self: flex-start;
    }
    #previewFrame {
        width: 100%;
        flex: 1;
        border: 1px solid #ccc;
        margin-top: 10px;
        background: white;
    }
</style>
</head>
<body>

<div class="container">
    <div class="row" style="margin-top:40px;">
        <div class="col-lg-6">
            <h3>Simülasyon</h3>
            <form method="post" action="add_simulation.post.php">
                
                <?php if(empty($_SESSION['csrf_token'])){$_SESSION['csrf_token']=bin2hex(random_bytes(32));}if(empty($_SESSION['form_time'])){$_SESSION['form_time']=time();} ?>
                <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token'];?>">
                <input type="text" name="website" style="display:none" autocomplete="off">

                <div class="form-group">
                    <label for="title">Başlık:</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Simülasyon başlığını giriniz.">
                </div>  
                
                <div class="form-group">
                    <label for="features">Simülasyon Özellikleri:</label>
                     <textarea type="text" class="form-control" id="features" name="features" placeholder="Simülasyon özellikleri giriniz."></textarea>
                </div> 

                <div class="form-group">
                    <label for="description">Simülasyon Açıklaması:</label>
                    <textarea class="form-control" name="description" id="description" placeholder="Neyi simüle ettiğini açıklayınız."></textarea>
                </div>		

                <div class="form-group">
                    <label for="html_code">HTML Kodu:</label>
                    <textarea class="form-control" name="html_code" id="html_code" placeholder="HTML kodunu buraya giriniz."></textarea>
                </div>

                <div class="form-group">
                    <label for="visibility">Simülasyon Görünürlüğü</label>
                    <select id="visibility" name="visibility" class="form-control">
                        <option value="1" selected>Tüm Simülasyonlar kısmında görünsün.</option>
                        <option value="2">Sadece profilde görünsün.</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Simülasyonu Gönder</button>

            </form>
        </div>

        <div class="col-lg-6">
            <h3>Önizleme</h3>
            <iframe id="previewFrame"></iframe>
        </div>
    </div>
</div>

<script>
    document.getElementById("html_code").addEventListener("input", function() {
        let html = this.value;
        let previewDoc = document.getElementById("previewFrame").contentDocument;
        previewDoc.open();
        previewDoc.write(html);
        previewDoc.close();
    });
</script>

</body>
</html>
