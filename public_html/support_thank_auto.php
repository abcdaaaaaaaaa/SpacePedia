<?php
session_start();

require_once(__DIR__.'/db_config.php');
require_once(__DIR__.'/template/mailer.php');

$lastFile=__DIR__.'/last_support_thanks.txt';

if(!file_exists($lastFile)||(time()-filemtime($lastFile))>86400){

    $stmt=$db->prepare("
        SELECT id,username,email,supporter,support_start,support_end
        FROM users
        WHERE supporter>0
          AND support_start IS NOT NULL
          AND support_start>=NOW()-INTERVAL 1 DAY
    ");
    $stmt->execute();
    $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($rows as $row){
        $username=$row['username'];
        $email=$row['email'];
        $supporter=(int)$row['supporter'];
        $startAt=$row['support_start'];
        $endAt=$row['support_end'];

        if($supporter===7){
            sendSupportThankYouForeverMail($email,$username);
        }else{
            sendSupportThankYouTimedMail($email,$username,$startAt,$endAt);
        }
    }

    file_put_contents($lastFile,date('Y-m-d H:i:s'));
}
?>