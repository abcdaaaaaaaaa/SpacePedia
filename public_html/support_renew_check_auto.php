<?php
session_start();

require_once(__DIR__.'/db_config.php');
require_once(__DIR__.'/template/mailer.php');

$lastFile=__DIR__.'/last_support_renew_check.txt';
$cacheFile=__DIR__.'/support_renew_cache.json';

if(!file_exists($lastFile)||(time()-filemtime($lastFile))>86400){

    $oldCache=[];
    if(file_exists($cacheFile)){
        $raw=file_get_contents($cacheFile);
        $decoded=json_decode($raw,true);
        if(is_array($decoded))$oldCache=$decoded;
    }

    $stmt=$db->prepare("SELECT id,username,email,supporter,support_end FROM users WHERE supporter>0");
    $stmt->execute();
    $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);

    $newCache=[];

    foreach($rows as $row){
        $id=(string)$row['id'];
        $username=$row['username'];
        $email=$row['email'];
        $supporter=(int)$row['supporter'];
        $supportEnd=$row['support_end'];

        $newCache[$id]=['supporter'=>$supporter,'support_end'=>$supportEnd];

        if(!isset($oldCache[$id]))continue;

        $oldSupporter=(int)($oldCache[$id]['supporter']??0);
        $oldEnd=$oldCache[$id]['support_end']??null;

        if($oldSupporter>=1&&$oldSupporter<=6&&$supporter===7){
            sendSupportRenewedUpgradedToForeverMail($email,$username);
            continue;
        }

        if($supporter>=1&&$supporter<=6){
            if((string)$oldEnd!==(string)$supportEnd){
                sendSupportRenewedTimedMail($email,$username,$supportEnd);
            }
        }
    }

    file_put_contents($cacheFile,json_encode($newCache,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    file_put_contents($lastFile,date('Y-m-d H:i:s'));
}
?>