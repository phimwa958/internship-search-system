<?php
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$faculty = trim($_POST['faculty'] ?? '');
$program = trim($_POST['program'] ?? '');
$major = trim($_POST['major'] ?? '');

if (!$faculty || !$program || !$major) {
    echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบทุกช่อง']);
    exit;
}

try {
    $pdo = db();
    $exists = db_fetch_all("SELECT id FROM faculty_program_major WHERE faculty = ? AND program = ? AND major = ?", [$faculty, $program, $major]);
    if ($exists) {
        echo json_encode(['success' => false, 'message' => 'รายการนี้มีอยู่ในระบบแล้ว']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO faculty_program_major (faculty, program, major) VALUES (?, ?, ?)");
    $stmt->execute([$faculty, $program, $major]);

    echo json_encode(['success' => true, 'message' => 'เพิ่มข้อมูลเรียบร้อย']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
