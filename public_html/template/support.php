<?php
session_start();
require_once('../db_config.php');

if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}

$L=[
'tr'=>[
'support_table'=>'Bağış Ayrıntıları',
'month'=>'1 Ay',
'forever'=>'Sonsuza Dek',
'no'=>'No',
'icon'=>'İkon',
'usd'=>'Minimum USD',
'euro'=>'Minimum EURO',
'table_time'=>'Süre',
'support'=>'Bağışa Devam Edin',
'badge_text'=>'Değerli desteğiniz, profilinizde parıldayan havalı bir ikon olarak görünür.',
'note_title'=>'Önemli Bilgilendirme',
'note_text'=>'Tablodaki tutarlar minimum değerlerdir (ör. 5+). İkon, kullanıcı tarafından seçilmez, yapılan bağış miktarına göre sistem tarafından belirlenir. Minimum tutarın üzerindeki bağışlar da geçerlidir ve gönlünüzden geçtiği kadar destek olabilirsiniz.'
],
'en'=>[
'support_table'=>'Donation Details',
'month'=>'1 Month',
'forever'=>'Forever',
'no'=>'ID',
'icon'=>'Icon',
'usd'=>'Minimum USD',
'euro'=>'Minimum EURO',
'table_time'=>'Duration',
'support'=>'Continue to Donation',
'badge_text'=>'Your valuable support will appear as a cool, glowing icon on your profile.',
'note_title'=>'Important Information',
'note_text'=>'Amounts in the table are minimum values (e.g., 5+). The icon is not chosen by the user, it is determined by the system based on the donation amount. Donations above the minimum are also valid — feel free to support as much as you wish.'
]
];

$supports=[
1=>['class'=>'support-1','gold'=>0.025,'days'=>30],
2=>['class'=>'support-2','gold'=>0.05,'days'=>30],
3=>['class'=>'support-3','gold'=>0.075,'days'=>30],
4=>['class'=>'support-4','gold'=>0.1,'days'=>30],
5=>['class'=>'support-5','gold'=>0.15,'days'=>30],
6=>['class'=>'support-6','gold'=>0.2,'days'=>30],
7=>['class'=>'support-7','gold'=>1,'days'=>0]
];

$q=$db->query("SELECT usd,euro,tl FROM gram_gold_prices ORDER BY updated_at DESC LIMIT 1");
$r=$q->fetch(PDO::FETCH_ASSOC);

$usd_rate=$r['usd'];
$euro_rate=$r['euro'];

function round_fx($v){
return ceil($v);
}
?>
<!DOCTYPE HTML>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $L[$lang]['support_table']; ?></title>

<link rel="shortcut icon" href="https://www.uzay.info/uzaylogo.ico">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">

<style>
.badge-wrap{width:90%;max-width:980px;margin:40px auto 14px auto;display:flex;justify-content:center}
.badge{position:relative;display:inline-flex;align-items:center;gap:10px;padding:12px 16px;border-radius:999px;background:#6600ff;color:#fff;font-weight:800;letter-spacing:.2px;box-shadow:0 0 22px rgba(102,0,255,.6)}
.badge:before{content:"";position:absolute;inset:-2px;border-radius:999px;background:radial-gradient(circle at center,#6600ff 0%,rgba(102,0,255,.9) 50%,rgba(102,0,255,.6) 80%,rgba(102,0,255,.25) 100%);filter:blur(10px);opacity:.9;z-index:-1}
.badge i{color:#fff;filter:drop-shadow(0 0 8px rgba(255,255,255,.6))}
.badge span{font-size:14px;opacity:.98}
table{width:90%;margin:0 auto 40px auto;border-collapse:collapse;background:#1e1e1e;color:#fff;border-radius:14px;overflow:auto;box-shadow:0 0 25px rgba(255,255,255,.35),0 0 60px rgba(255,255,255,.15);transition:.3s}
table:hover{box-shadow:0 0 45px rgba(255,255,255,.6),0 0 100px rgba(255,255,255,.3)}
th,td{padding:14px;text-align:center;font-size:16px;border-bottom:1px solid rgba(255,255,255,.15);transition:.3s}
th{background:#072768}
td i{font-size:18px;display:inline-block;transition:.35s}
.support-1{color:#c0c0c0;filter:drop-shadow(0 0 6px rgba(192,192,192,.6))}
.support-2{color:#2ecc71;filter:drop-shadow(0 0 8px rgba(46,204,113,.7))}
.support-3{color:#3498db;filter:drop-shadow(0 0 10px rgba(52,152,219,.75))}
.support-4{color:#9b59b6;filter:drop-shadow(0 0 12px rgba(155,89,182,.8))}
.support-5{color:#f1c40f;filter:drop-shadow(0 0 14px rgba(241,196,15,.85))}
.support-6{color:#e74c3c;filter:drop-shadow(0 0 18px rgba(231,76,60,.95))}
.support-7{color:#e74c3c;filter:drop-shadow(0 0 18px rgba(231,76,60,.95))}
td i:hover{transform:translateY(-14px) scale(1.3);filter:drop-shadow(0 0 36px currentColor)}
button{padding:10px 20px;border:none;border-radius:10px;background:#00aaff;color:#fff;font-weight:700;cursor:pointer;transition:.3s}
button:hover{background:#0088cc;box-shadow:0 0 18px rgba(0,170,255,.9)}
.support-btn{display:flex;justify-content:center;margin:12px auto 40px auto}
.note-box{width:90%;max-width:980px;margin:0 auto 14px auto;background:#111;border-radius:14px;padding:16px 18px;color:#fff;box-shadow:inset 0 0 0 1px rgba(255,255,255,.08)}
.note-title{color:#00aaff;font-weight:800;margin-bottom:6px}
.note-text{font-size:14px;line-height:1.65;opacity:.95}
</style>
</head>
<body>

<div class="badge-wrap">
<div class="badge"><span><?php echo $L[$lang]['badge_text']; ?></span></div>
</div>

<table>
<tr>
<th><?php echo $L[$lang]['no']; ?></th>
<th><?php echo $L[$lang]['icon']; ?></th>
<th><?php echo $L[$lang]['usd']; ?></th>
<th><?php echo $L[$lang]['euro']; ?></th>
<th><?php echo $L[$lang]['table_time']; ?></th>
</tr>

<?php foreach($supports as $id=>$s):

$usd_raw=$s['gold']*$usd_rate;
$euro_raw=$s['gold']*$euro_rate;

$usd_val=round_fx($usd_raw);
$euro_val=round_fx($euro_raw);
?>

<tr>
<td><?php echo $id; ?></td>
<td><i class="fa-solid fa-gem <?php echo $s['class']; ?>"></i></td>
<td><?php echo number_format($usd_val,0,'.',','); ?>+</td>
<td><?php echo number_format($euro_val,0,'.',','); ?>+</td>
<td><?php echo $s['days']>0?$L[$lang]['month']:$L[$lang]['forever']; ?></td>
</tr>

<?php endforeach; ?>
</table>

<div class="support-btn">
<form action="/support_success" method="post" target="_blank">
<button type="submit"><?php echo $L[$lang]['support']; ?></button>
</form>
</div>

<div class="note-box">
<div class="note-title"><?php echo $L[$lang]['note_title']; ?></div>
<div class="note-text"><?php echo $L[$lang]['note_text']; ?></div>
</div>

</body>
</html>
