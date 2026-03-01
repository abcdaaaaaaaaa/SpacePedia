<?php

require_once __DIR__.'/db_config.php';

$lastUpdateFile = __DIR__.'/last_gram_gold_prices.txt';

if (!file_exists($lastUpdateFile) || (time() - filemtime($lastUpdateFile)) > 604800) {

    $altinJson = file_get_contents('https://api.genelpara.com/json/?list=altin&sembol=GA');
    $dovizJson = file_get_contents('https://api.genelpara.com/json/?list=doviz&sembol=USD,EUR');

    $altinData = json_decode($altinJson, true);
    $dovizData = json_decode($dovizJson, true);

    $tl = (float)$altinData['data']['GA']['satis'];
    $usdTry = (float)$dovizData['data']['USD']['satis'];
    $eurTry = (float)$dovizData['data']['EUR']['satis'];

    $usd = $tl / $usdTry;
    $euro = $tl / $eurTry;

    $stmt = $db->prepare("INSERT INTO gram_gold_prices (usd, euro, tl, updated_at) VALUES (?,?,?,NOW())");
    $stmt->execute([$usd, $euro, $tl]);

    file_put_contents($lastUpdateFile, date('Y-m-d H:i:s'));
}

?>
