<?php
session_start();
require_once('../db_config.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id <= 0){
    die("Invalid simulation ID.");
}

try {
    $stmt = $db->prepare("SELECT * FROM simulation_posts2 WHERE id=:id LIMIT 1");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $sim = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$sim){
        die("Simulation not found.");
    }
} catch (PDOException $e){
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($sim['title']); ?></title>
<style>
html, body { margin:0; padding:0; height:100%; width:100%; overflow:auto; }
iframe { border:none; width:100%; height:100%; display:block; }
</style>
</head>
<body>
<iframe srcdoc="<?php echo htmlspecialchars($sim['html_code']); ?>"></iframe>
</body>
</html>
