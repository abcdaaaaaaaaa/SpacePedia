<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');
require_once('db_config.php');

if(!isset($_SESSION['user_id'])){echo json_encode(['ok'=>false]);exit();}
if($_SERVER['REQUEST_METHOD']!=='POST'){echo json_encode(['ok'=>false]);exit();}

$mode=$_POST['mode'] ?? '';
if($mode!=='light' && $mode!=='dark'){echo json_encode(['ok'=>false]);exit();}

$stmt=$db->prepare("UPDATE users SET mode=? WHERE id=?");
$ok=$stmt->execute([$mode,(int)$_SESSION['user_id']]);

echo json_encode(['ok'=>$ok?true:false]);
?>
