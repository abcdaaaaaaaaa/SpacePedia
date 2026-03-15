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
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
<link rel="stylesheet" href="https://uzay.info/template/backgroundonly.css">
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-lg-offset-2 col-lg-8" style="margin-top:40px;">
            <h3>Forum</h3>
            <br><br>
            <form method="post" action="add_forum_post.php">

                <?php if(empty($_SESSION['csrf_token'])){$_SESSION['csrf_token']=bin2hex(random_bytes(32));}if(empty($_SESSION['form_time'])){$_SESSION['form_time']=time();} ?>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" name="website" style="display:none" autocomplete="off">

                <div class="form-group">
                    <label for="title">Başlık</label>
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
                        <option value="5" selected="selected">Genel Uzay</option>
                        <option value="4">Kazalar</option>
                        <option value="3">Öneriler</option>
                        <option value="2">Problemler</option>
                        <option value="1">Yeni Gelişmeler</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Mesajınız</label>
                    <textarea class="form-control" name="message" id="message" rows="5" placeholder="Mesajınızı yazınız."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Formu Gönder</button>

            </form>
        </div>
    </div>
</div>

</body>
</html>
