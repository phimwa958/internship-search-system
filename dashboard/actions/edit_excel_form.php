<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';

$baseUrl = $_ENV['BASE_URL'] ?? '';

// Set the filename for the wrong records file
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=internship_report (ข้อมูลที่ไม่ถูกต้อง).csv');

// Write the csv file
$output = fopen('php://output', 'w');
fwrite($output, "\xEF\xBB\xBF");

// Set the column name for the file
fputcsv($output, ['บริษัท', 'จังหวัด', 'คณะ', 'หลักสูตร', 'สาขา', 'ปีการศึกษา', 'สังกัด', 'จำนวนที่รับ', 'MOU', 'ข้อมูลการติดต่อ', 'คะแนน']);

// Write the wrong data to the file
if (isset($_SESSION['invalid_rows']) && count($_SESSION['invalid_rows']) > 0) {
    foreach ($_SESSION['invalid_rows'] as $row) {
        fputcsv($output, [
            $row['organization'] ?? '',
            $row['province'] ?? '',
            $row['faculty'] ?? '',
            $row['program'] ?? '',
            $row['major'] ?? '',
            $row['year'] ?? '',
            $row['affiliation'] ?? '',
            $row['total_student'] ?? '',
            $row['mou_status'] ?? '',
            $row['contact'] ?? '',
            $row['score'] ?? '',
        ]);
    }
} else {
    fputcsv($output, ['ไม่มีข้อมูลผิดพลาดใน Session']);
}

fclose($output);
// unset($_SESSION['invalid_rows']);
exit;
