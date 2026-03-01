<?php
require_once __DIR__.'/db_config.php';

$lastCleanupFile = __DIR__.'/last_support_cleanup.txt';

if (!file_exists($lastCleanupFile) || (time() - filemtime($lastCleanupFile)) > 86400) {

    $stmt = $db->prepare("
        UPDATE users
        SET supporter = 0,
            support_start = NULL,
            support_end = NULL
        WHERE supporter > 0
          AND support_end IS NOT NULL
          AND support_end < NOW() - INTERVAL 2 DAY
    ");
    $stmt->execute();

    file_put_contents($lastCleanupFile, date('Y-m-d H:i:s'));
}
?>
