<?php
require_once __DIR__ . '/../includes/functions.php';

if (ob_get_length())
    ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

$response = ['status' => 'error', 'message' => 'มีบางอย่างผิดพลาด'];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    $isUseful = $_POST['is_useful'] ?? null;
    $commentRaw = $_POST['comment'] ?? '';

    if (empty($isUseful))
        throw new Exception('กรุณาระบุว่ามีประโยชน์หรือไม่');
    if (!in_array($isUseful, ['มีประโยชน์', 'ไม่มีประโยชน์']))
        throw new Exception('ข้อมูล "is_useful" ไม่ถูกต้อง');
    if (mb_strlen($commentRaw, 'UTF-8') > 200)
        throw new Exception('ข้อเสนอแนะต้องไม่เกิน 200 ตัวอักษร');

    $comment = empty($commentRaw) ? null : $commentRaw;
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

    $pdo = db();

    // Check for recent feedback within 1 hour
    $sqlCheck = "SELECT id FROM feedback 
                 WHERE ip_address = ? 
                 AND created_at >= NOW() - INTERVAL 1 HOUR 
                 LIMIT 1";

    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([$ipAddress]);

    if ($stmtCheck->rowCount() > 0) {
        throw new Exception('คุณได้ส่ง Feedback ไปแล้ว กรุณารอสักครู่ก่อนส่งใหม่อีกครั้ง');
    }

    $sql = "INSERT INTO feedback (is_useful, comment, ip_address) 
            VALUES (?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$isUseful, $comment, $ipAddress])) {
        $response['status'] = 'success';
        $response['message'] = 'ขอบคุณสำหรับข้อเสนอแนะ!';
    } else {
        throw new Exception('เกิดข้อผิดพลาดในการบันทึกข้อมูล');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
