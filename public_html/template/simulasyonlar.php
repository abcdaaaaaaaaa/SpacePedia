<?php
session_start();
require_once('../db_config.php');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tüm Simülasyonlar</title>
<link rel="stylesheet" href="https://uzay.info/template/backgroundonly.css">
<style>
body{font-family:Arial,sans-serif;margin:0;padding:0;}
header{background-color:#072768;color:white;padding:.6em 1em;margin-bottom:15px;display:flex;align-items:center;justify-content:space-between;position:relative;}
header h1{margin:0;font-size:20px;text-align:center;position:absolute;left:50%;transform:translateX(-50%);}
header a.logo{color:white;text-decoration:none;font-weight:bold;font-size:18px;margin-right:15px;}
header form{margin:0;display:flex;gap:5px;align-items:center;}
header input[type="text"]{padding:6px;border-radius:5px;border:none;width:200px;}
header button{padding:6px 12px;border:none;border-radius:5px;background:#ff9800;color:white;cursor:pointer;}
header button:hover{background:#e68900;}
.container{display:flex;flex-wrap:wrap;justify-content:center;gap:15px;}
.sim-box{border:1px solid #ddd;border-radius:5px;width:calc(25% - 15px);background:white;box-shadow:0 0 10px rgba(0,0,0,0.1);cursor:pointer;display:flex;flex-direction:column;transition:transform .3s;text-decoration:none;color:inherit;}
.sim-box:hover{transform:scale(1.05);}
.sim-preview{width:100%;height:150px;border-bottom:1px solid #ccc;background:#f9f9f9;;display:flex;justify-content:center;align-items:center;}
.sim-preview iframe{width:100%;height:100%;border:none;pointer-events:none;}
.sim-info{padding:10px;flex:1;display:flex;flex-direction:column;}
.sim-info h2{font-size:16px;margin:2px 0;white-space:nowrap;text-overflow:ellipsis;}
.sim-info p{font-size:12px;margin:2px 0;white-space:normal;}
.sim-info p b{white-space:nowrap;}
.no-sims{display:flex;justify-content:center;align-items:center;height:70vh;font-size:22px;color:#555;font-weight:bold;text-align:center;}
@media(max-width:1200px){.sim-box{width:calc(33.33% - 15px);}}
@media(max-width:800px){.sim-box{width:calc(50% - 15px);}}
@media(max-width:500px){.sim-box{width:calc(100% - 15px);}}
</style>
</head>
<body>
<header>
<a href="/" class="logo">uzay.info</a>
<h1>Tüm Simülasyonlar</h1>
<form method="get" action="">
    <input type="text" name="search" placeholder="Simülasyon ara..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Ara</button>
</form>
</header>

<div class="container">
<?php
try {
    $params = [];
    $sql = "SELECT simulation_posts.*, users.username 
            FROM simulation_posts 
            LEFT JOIN users ON simulation_posts.user_id = users.id 
            WHERE simulation_posts.visibility = 1";
    if ($search) {
        $sql .= " AND (
            simulation_posts.title LIKE :search 
            OR simulation_posts.features LIKE :search 
            OR simulation_posts.description LIKE :search
            OR users.username LIKE :search
            OR DATE_FORMAT(simulation_posts.created_at, '%d-%m-%Y') LIKE :search
        )";
        $params[':search'] = "%$search%";
    }
    $sql .= " ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $sims = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($sims) {
        foreach ($sims as $sim) {
            $detailLink = '/simulasyondetay?id='.$sim['id'];
            echo '<a class="sim-box" href="'.$detailLink.'" target="_blank">';
            echo '<div class="sim-preview">';
            if(!empty($sim['html_code'])){
                echo '<iframe srcdoc="'.htmlspecialchars($sim['html_code']).'"></iframe>';
            } else {
                echo '<div style="text-align:center; line-height:150px; color:#999;">Önizleme yok</div>';
            }
            echo '</div>';
            echo '<div class="sim-info">';
            echo '<h2>'.htmlspecialchars($sim['title']).'</h2>';
            echo '<p><b>Yazar:</b> '.htmlspecialchars($sim['username']).'</p>';
            echo '<p><b>Özellikler:</b> '.htmlspecialchars($sim['features']).'</p>';
            echo '<p><b>Açıklamalar:</b> '.htmlspecialchars($sim['description']).'</p>';
            echo '<p><b>Tarih:</b> '.htmlspecialchars(date("d-m-Y", strtotime($sim['created_at']))).'</p>';
            echo '</div></a>';
        }
    } else {
        echo "<div class='no-sims'>Arama kriterine uygun simülasyon bulunamadı.</div>";
    }
} catch (PDOException $e) {
    echo "<div class='no-sims'>Veritabanı Hatası: ".$e->getMessage()."</div>";
}
?>
</div>
</body>
</html>