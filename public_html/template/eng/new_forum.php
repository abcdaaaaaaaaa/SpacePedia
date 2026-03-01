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
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter the title.">
                </div>

                <div class="form-group">
                    <label for="theme">Theme</label>
                    <select id="theme" name="theme" class="form-control">
                        <option value="13">Black Holes</option>
                        <option value="12">Galaxies</option>
                        <option value="11">Neutron Stars</option>
                        <option value="10">Comets</option>
                        <option value="9">Constellations</option>
                        <option value="8">Stars</option>
                        <option value="7">Planets</option>
                        <option value="6">Nebulae</option>
                        <option value="5" selected="selected">General Space</option>
                        <option value="4">Accidents</option>
                        <option value="3">Suggestions</option>
                        <option value="2">Problems</option>
                        <option value="1">New Updates</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Your message</label>
                    <textarea class="form-control" name="message" id="message" rows="5" placeholder="Write your message."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit Forum</button>

            </form>
        </div>
    </div>
</div>

</body>
</html>
