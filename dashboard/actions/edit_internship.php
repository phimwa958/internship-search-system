<?php
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$organization = trim($_POST['organization'] ?? '');
$province = trim($_POST['province'] ?? '');
$faculty = trim($_POST['faculty'] ?? '');
$program = trim($_POST['program'] ?? '');
$major = trim($_POST['major'] ?? '');
$yearInput = (int) ($_POST['year'] ?? 0);
$totalStudentInput = (int) ($_POST['total_student'] ?? 0);
$mouStatus = trim($_POST['mou_status'] ?? '');
$score = trim($_POST['score'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$affiliation = trim($_POST['affiliation'] ?? '');

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

try {
    $pdo = db();
    $current = db_fetch_all("SELECT * FROM internship_stats WHERE id = ?", [$id])[0] ?? null;

    if (!$current) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล']);
        exit;
    }

    $majorRow = db_fetch_all("SELECT id FROM faculty_program_major WHERE faculty = ? AND program = ? AND major = ? LIMIT 1", [$faculty, $program, $major]);
    if (!$majorRow) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลคณะ/สาขา']);
        exit;
    }

    $majorId = $majorRow[0]['id'];
    $sql = "UPDATE internship_stats SET major_id=?, organization=?, province=?, year=?, total_student=?, mou_status=?, score=?, contact=?, affiliation=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $majorId,
        $organization,
        $province,
        $yearInput ?: $current['year'],
        $totalStudentInput ?: $current['total_student'],
        $mouStatus ?: $current['mou_status'],
        $score,
        $contact,
        $affiliation ?: $current['affiliation'],
        $id
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
