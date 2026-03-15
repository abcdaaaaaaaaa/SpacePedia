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
            <h3>Simulation</h3>
            <form method="post" action="add_simulation.post.php">
                
                <?php if(empty($_SESSION['csrf_token'])){$_SESSION['csrf_token']=bin2hex(random_bytes(32));}if(empty($_SESSION['form_time'])){$_SESSION['form_time']=time();} ?>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" name="website" style="display:none" autocomplete="off">

                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter the simulation title.">
                </div>  
                
                <div class="form-group">
                    <label for="features">Simulation Feature:</label>
                     <textarea type="text" class="form-control" id="features" name="features" placeholder="Enter the simulation features."></textarea>
                </div> 

                <div class="form-group">
                    <label for="description">Simulation Description:</label>
                    <textarea class="form-control" name="description" id="description" placeholder="Describe what the simulation represents."></textarea>
                </div>		

                <div class="form-group">
                    <label for="html_code">HTML Code:</label>
                    <textarea class="form-control" name="html_code" id="html_code" placeholder="Enter the HTML code here"></textarea>
                </div>

                <div class="form-group">
                    <label for="visibility">Simulation Gorunurlugu</label>
                    <select id="visibility" name="visibility" class="form-control">
                        <option value="1" selected>Visible in All Simulations.</option>
                        <option value="2">Visible on profile only.</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Submit Simulation</button>

            </form>
        </div>

        <div class="col-lg-6">
            <h3>Preview</h3>
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
