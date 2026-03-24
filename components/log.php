<?php
require_once __DIR__ . '/../includes/functions.php';

$userId = $_SESSION['user_id'] ?? NULL;
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

try {
    $pdo = db();

    // Check for recent log within 1 hour
    $sqlCheck = "SELECT COUNT(*) FROM access_logs 
                 WHERE ip_address = ? 
                 AND created_at >= NOW() - INTERVAL 1 HOUR";

    if (db_count($sqlCheck, [$ipAddress]) === 0) {
        $sqlInsert = "INSERT INTO access_logs (user_id, ip_address, user_agent) 
                      VALUES (?, ?, ?)";
        db()->prepare($sqlInsert)->execute([$userId, $ipAddress, $userAgent]);
    }
} catch (Exception $e) {
    // Silent fail for logging
}
