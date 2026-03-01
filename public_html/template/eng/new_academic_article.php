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
            <h3>Academic Article</h3>
            <form method="post" action="add_article_post.php" enctype="multipart/form-data">
                
                <?php if(empty($_SESSION['csrf_token'])){$_SESSION['csrf_token']=bin2hex(random_bytes(32));}if(empty($_SESSION['form_time'])){$_SESSION['form_time']=time();} ?>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" name="website" style="display:none" autocomplete="off">

                <div class="form-group">
                    <label for="cover">Academic Article Cover Image</label>
                    <input class="file-upload-input" id="cover" name="cover" type="file" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="pdf">Academic Article PDF File</label>
                    <input class="file-upload-input" id="pdf" name="pdf" type="file" accept=".pdf">
                </div>

                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="You can enter the article title.">
                </div>  
                
                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input type="text" class="form-control" id="subject" name="subject" placeholder="You can enter the article topic.">
                </div> 

                <div class="form-group">
                    <label for="summary">Article Summary:</label>
                    <textarea class="form-control" name="summary" id="summary" placeholder="We request a summary of the main idea to be discussed in the article."></textarea>
                </div>		
                
                <div class="form-group">
                    <label for="purpose">Article Purpose:</label>
                    <textarea class="form-control" name="purpose" id="purpose" placeholder="Briefly state the purpose of the article."></textarea>
                </div>		
                
                <div class="form-group">
                    <label for="audience">Article Target Audience:</label>
                    <input type="text" class="form-control" id="audience" name="audience" placeholder="Briefly state which target audience you intended to inform by writing this article.">
                </div>  
                
                <div class="form-group">
                    <label for="visibility">Article Visibility</label>
                    <select id="visibility" name="visibility" class="form-control">
                        <option value="1" selected>Visible in All Academic Articles</option>
                        <option value="2">Visible on profile only.</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Submit Academic Article</button>

            </form>
        </div>
    </div>
</div>

</body>
</html>