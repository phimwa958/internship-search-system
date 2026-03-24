<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$baseUrl = $_ENV['BASE_URL'] ?? '';

if (isset($_POST['submit'])) {
    unset($_SESSION['invalid_rows']);

    $fileTmpPath = $_FILES['excel_file']['tmp_name'];
    $fileName = $_FILES['excel_file']['name'];
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);

    if (in_array($extension, ['csv', 'xls', 'xlsx'])) {
        try {
            $spreadsheet = IOFactory::load($fileTmpPath);
            $data = $spreadsheet->getActiveSheet()->toArray();
            $pdo = db();

            $count = 0;
            foreach ($data as $row) {
                if ($count === 0) { // Skip header
                    $count++;
                    continue;
                }

                $organization = trim($row[0] ?? '');
                $province = trim($row[1] ?? '');
                $faculty = trim($row[2] ?? '');
                $program = trim($row[3] ?? '');
                $major = trim($row[4] ?? '');
                $year = trim($row[5] ?? '');
                $affiliation = trim($row[6] ?? '');
                $totalStudent = trim($row[7] ?? '');
                $mouStatus = trim($row[8] ?? '');
                $contact = trim($row[9] ?? '');
                $score = trim($row[10] ?? '');

                if (empty($organization) || empty($province) || empty($faculty) || empty($program) || empty($major)) {
                    $_SESSION['invalid_rows'][] = array_combine(
                        ['organization', 'province', 'faculty', 'program', 'major', 'year', 'affiliation', 'total_student', 'mou_status', 'contact', 'score'],
                        [$organization, $province, $faculty, $program, $major, $year, $affiliation, $totalStudent, $mouStatus, $contact, $score]
                    );
                    continue;
                }

                $majorRow = db_fetch_all("
                    SELECT id FROM faculty_program_major 
                    WHERE faculty = ? AND program = ? AND major = ? 
                    LIMIT 1
                ", [$faculty, $program, $major]);

                if ($majorRow) {
                    $majorId = $majorRow[0]['id'];
                    $sql = 'INSERT INTO internship_stats 
                            (organization, province, major_id, year, affiliation, total_student, mou_status, contact, score)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$organization, $province, $majorId, $year, $affiliation, $totalStudent, $mouStatus, $contact, $score]);

                    $_SESSION['inserted_data'][] = [
                        'organization' => $organization,
                        'province' => $province,
                        'faculty' => $faculty,
                        'program' => $program,
                        'major' => $major,
                        'year' => $year,
                        'affiliation' => $affiliation,
                        'total_student' => $totalStudent,
                        'mou_status' => $mouStatus,
                        'contact' => $contact,
                        'score' => $score
                    ];
                    $msg = true;
                } else {
                    $_SESSION['invalid_rows'][] = array_combine(
                        ['organization', 'province', 'faculty', 'program', 'major', 'year', 'affiliation', 'total_student', 'mou_status', 'contact', 'score'],
                        [$organization, $province, $faculty, $program, $major, $year, $affiliation, $totalStudent, $mouStatus, $contact, $score]
                    );
                }
            }

            $_SESSION['message'] = isset($msg) ? "นำเข้าข้อมูลสำเร็จ" : "นำเข้าข้อมูลไม่สำเร็จ";
        } catch (Exception $e) {
            $_SESSION['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }

        header("Location: {$baseUrl}/dashboard/insert_excel.php");
        exit;
    }
}
