<?php
session_start();

require_once(__DIR__.'/db_config.php');
require_once(__DIR__.'/template/mailer.php');

$lastFile=__DIR__.'/last_support_expired.txt';

if(!file_exists($lastFile)||(time()-filemtime($lastFile))>172800){

    $stmt=$db->prepare("
        SELECT id,username,email,support_end,
        FLOOR(GREATEST(TIMESTAMPDIFF(SECOND,NOW(),support_end+INTERVAL 2 DAY)/3600,0)) AS removalStartHours
        FROM users
        WHERE supporter BETWEEN 1 AND 6
          AND support_end IS NOT NULL
          AND support_end<=NOW()
          AND support_end+INTERVAL 2 DAY>=NOW()
    ");
    $stmt->execute();
    $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($rows as $row){
        $username=$row['username'];
        $email=$row['email'];
        $removalStartHours=(int)$row['removalStartHours'];
        sendSupportExpiredReminderMail($email,$username,$removalStartHours);
    }

    file_put_contents($lastFile,date('Y-m-d H:i:s'));
}
?>