<?php
session_start();
if(!isset($_SESSION['username'])){
header("Location:/login");
exit;
}
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
            <h3>Course</h3>
            <form method="post" action="add_course_post.php" enctype="multipart/form-data">
                
                <?php if(empty($_SESSION['csrf_token'])){$_SESSION['csrf_token']=bin2hex(random_bytes(32));}if(empty($_SESSION['form_time'])){$_SESSION['form_time']=time();} ?>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" name="website" style="display:none" autocomplete="off">

                <div class="form-group">
                    <label for="intro_video">Course Promotional Video Link:</label>
                    <input type="url" class="form-control" id="intro_video" name="intro_video" placeholder="Enter the Course Promotional Video link.">
                    <p class="help-block">The preview image from the promotional video will be used as the course cover image.</p>
                    <p class="help-block">A YouTube link is required only for the promo video.</p>
                </div>

                <div class="form-group">
                    <label for="title">Course Title:</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter the course title.">
                </div>  
                
                <div class="form-group">
                    <label for="description">Course Description:</label>
                    <textarea class="form-control" name="description" id="description" placeholder="A brief summary of the topics that will be covered in the course."></textarea>
                </div>		

                <div class="form-group">
                    <label for="audience">Course Target Audience:</label>
                    <input type="text" class="form-control" id="audience" name="audience" placeholder="Which audience is this course for?">
                </div>  

                <div class="form-group">
                    <label for="section_count">Course Number of Sections:</label>
                    <input type="number" class="form-control" id="section_count" name="section_count" min="1" placeholder="Enter how many sections it has.">
                </div>  

                <div id="videoLinks"></div>

                <script>
                    document.getElementById("section_count").addEventListener("input", function() {
                        let count = this.value;
                        let container = document.getElementById("videoLinks");
                        container.innerHTML = "";
                        for (let i = 1; i <= count; i++) {
                            let div = document.createElement("div");
                            div.className = "form-group";
                            div.innerHTML = `
                                <label for="video_${i}">Section ${i} Video Link:</label>
                                <input type="url" class="form-control" id="video_${i}" name="videos[]" placeholder="Enter the video link for Section ${i}.">
                            `;
                            container.appendChild(div);
                        }
                    });
                </script>

                <button type="submit" class="btn btn-success">Submit Course</button>

            </form>
        </div>
    </div>
</div>

</body>
</html>
