<?php
$servername = "localhost";
$dbname = "uzayinfo_user";
$username = "uzayinfo_hellouser";
$password = "myenglishisenglish";

try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Something went wrong: " . $e->getMessage());
}

define('STRIPE_SECRET_KEY','sk_live_DEGISTIR');
?>