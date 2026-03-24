<?php
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$faculty = trim($_POST['faculty'] ?? '');
$program = trim($_POST['program'] ?? '');
$major = trim($_POST['major'] ?? '');

if ($id <= 0 || !$faculty || !$program || !$major) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

try {
    $pdo = db();
    $exists = db_fetch_all("SELECT id FROM faculty_program_major WHERE faculty = ? AND program = ? AND major = ? AND id != ?", [$faculty, $program, $major, $id]);
    if ($exists) {
        echo json_encode(['success' => false, 'message' => 'มีรายการเดียวกันอยู่แล้วในระบบ']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE faculty_program_major SET faculty = ?, program = ?, major = ? WHERE id = ?");
    $stmt->execute([$faculty, $program, $major, $id]);

    echo json_encode(['success' => true, 'message' => 'อัปเดตข้อมูลเรียบร้อย']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
