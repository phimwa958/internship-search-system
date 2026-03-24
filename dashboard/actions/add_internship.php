<?php
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$organization = trim($_POST['organization'] ?? '');
$province = trim($_POST['province'] ?? '');
$faculty = trim($_POST['faculty'] ?? '');
$program = trim($_POST['program'] ?? '');
$major = trim($_POST['major'] ?? '');
$year = (int) ($_POST['year'] ?? 0);
$affiliation = trim($_POST['affiliation'] ?? '');
$totalStudent = (int) ($_POST['total_student'] ?? 0);
$mouStatus = trim($_POST['mou_status'] ?? '');
$score = trim($_POST['score'] ?? '');
$contact = trim($_POST['contact'] ?? '');

if (!$organization || !$province || !$faculty || !$year || !$totalStudent || !$mouStatus) {
    echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
    exit;
}

if ($score !== '' && (!is_numeric($score) || $score < 0 || $score > 5)) {
    echo json_encode(['success' => false, 'message' => 'คะแนนต้องอยู่ระหว่าง 0 - 5']);
    exit;
}

try {
    $pdo = db();
    $majorRow = db_fetch_all("SELECT id FROM faculty_program_major WHERE faculty = ? AND program = ? AND major = ? LIMIT 1", [$faculty, $program, $major]);

    if (!$majorRow) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลคณะ/หลักสูตร/สาขา']);
        exit;
    }

    $majorId = $majorRow[0]['id'];
    $sql = "INSERT INTO internship_stats (organization, province, major_id, year, affiliation, total_student, mou_status, contact, score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$organization, $province, $majorId, $year, $affiliation, $totalStudent, $mouStatus, $contact, $score ?: "0"]);

    echo json_encode(['success' => true, 'message' => 'เพิ่มข้อมูลสำเร็จ']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
