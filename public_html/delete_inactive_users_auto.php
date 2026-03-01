<?php
require_once __DIR__.'/db_config.php';

$lastCleanupFile = __DIR__.'/last_cleanup.txt';

if (!file_exists($lastCleanupFile) || (time() - filemtime($lastCleanupFile)) > 86400) {

    $stmt = $db->prepare("
        DELETE FROM users
        WHERE email_verified = 0
          AND created_at < NOW() - INTERVAL 2 DAY
    ");
    $stmt->execute();

    file_put_contents($lastCleanupFile, date('Y-m-d H:i:s'));
}
?>