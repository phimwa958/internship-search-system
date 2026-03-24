<?php
require_once __DIR__ . '/../../includes/functions.php';

$baseUrl = $_ENV['BASE_URL'] ?? '';

// Set the filename for the wrong records file
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=แบบฟอร์มเก็บข้อมูลนักศึกษาฝึกงาน.csv');

// Write the csv file
$output = fopen('php://output', 'w');
fwrite($output, "\xEF\xBB\xBF");

// Set the column name for the file
fputcsv($output, ['บริษัท', 'จังหวัด', 'คณะ', 'หลักสูตร', 'สาขา', 'ปีการศึกษา', 'สังกัด', 'จำนวนที่รับ', 'MOU', 'ข้อมูลการติดต่อ', 'คะแนน']);

fclose($output);
// unset($_SESSION['invalid_rows']);
exit;
