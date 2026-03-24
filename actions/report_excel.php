<?php
require_once __DIR__ . '/../includes/functions.php';

$faculty = $_GET['faculty'] ?? null;
$program = $_GET['program'] ?? null;
$major = $_GET['major'] ?? null;
$province = $_GET['province'] ?? null;
$academicYear = $_GET['academic-year'] ?? null;

$whereClause = [];
$params = [];

if ($faculty) {
    $whereClause[] = 'fpm.faculty = ?';
    $params[] = $faculty;
}
if ($program) {
    $whereClause[] = 'fpm.program = ?';
    $params[] = $program;
}
if ($major) {
    $whereClause[] = 'fpm.major = ?';
    $params[] = $major;
}
if ($province) {
    $whereClause[] = 'ist.province = ?';
    $params[] = $province;
}
if ($academicYear) {
    $whereClause[] = 'ist.year = ?';
    $params[] = $academicYear;
}

$whereSql = !empty($whereClause) ? 'WHERE ' . implode(' AND ', $whereClause) : '';

try {
    $sql = "
        SELECT ist.*, fpm.faculty, fpm.program, fpm.major
        FROM internship_stats ist
        LEFT JOIN faculty_program_major fpm ON ist.major_id = fpm.id
        $whereSql
        ORDER BY ist.id DESC
    ";

    $data = db_fetch_all($sql, $params);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=internship_report.csv');

    $output = fopen('php://output', 'w');
    fwrite($output, "\xEF\xBB\xBF");

    fputcsv($output, ['บริษัท', 'จังหวัด', 'คณะ', 'หลักสูตร', 'สาขา', 'ปีการศึกษา', 'สังกัด', 'จำนวนที่รับ', 'MOU', 'ข้อมูลการติดต่อ', 'คะแนน']);

    foreach ($data as $row) {
        fputcsv($output, [
            $row['organization'],
            $row['province'],
            $row['faculty'],
            $row['program'],
            $row['major'],
            $row['year'],
            $row['affiliation'],
            $row['total_student'],
            $row['mou_status'],
            $row['contact'],
            $row['score']
        ]);
    }
    fclose($output);
    exit;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
