<?php
ini_set('max_execution_time', '300');
ini_set('memory_limit', '4096M');
require_once __DIR__ . '/../includes/functions.php';

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

$faculty = $_POST['faculty'] ?? null;
$program = $_POST['program'] ?? null;
$major = $_POST['major'] ?? null;
$province = $_POST['province'] ?? null;
$academicYear = $_POST['academic-year'] ?? null;

$defaultConfig = (new ConfigVariables())->getDefaults();
$fontDirs = $defaultConfig['fontDir'];

$defaultFontConfig = (new FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];

$tmpDir = dirname(__DIR__) . '/tmp/mpdf';
if (!is_dir($tmpDir)) {
    mkdir($tmpDir, 0777, true);
}

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'UTF-8',
    'format' => 'A4',
    'orientation' => 'L',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_bottom' => 16,
    'margin_header' => 9,
    'margin_footer' => 9,
    'fontDir' => array_merge($fontDirs, [dirname(__DIR__) . '/public/fonts']),
    'fontdata' => $fontData + [
        'sarabun' => [
            'R' => 'THSarabunNew.ttf',
            'B' => 'THSarabunNew Bold.ttf',
            'I' => 'THSarabunNew Italic.ttf',
            'BI' => 'THSarabunNew BoldItalic.ttf',
        ],
    ],
    'default_font' => 'sarabun',
    'autoScriptToLang' => true,
    'autoLangToFont' => true,
    'tempDir' => $tmpDir
]);

$mpdf->SetTitle('รายงานประวัติการฝึกงาน');
$mpdf->defaultfooterline = 0;
$mpdf->setFooter('<div style="font-family: sarabun, sans-serif; font-size: 14pt; border-top: none;">{PAGENO} / {nbpg}</div>');

try {
    $pdo = db();
    $sql = "
        SELECT
            fpm.faculty, fpm.program, fpm.major,
            ist.organization, ist.province, ist.year,
            ist.affiliation, ist.total_student, ist.mou_status,
            ist.contact, ist.score
        FROM internship_stats ist
        INNER JOIN faculty_program_major fpm ON ist.major_id = fpm.id
        ORDER BY ist.year DESC
    ";
    $rows = db_fetch_all($sql);

    $uniqueCompanyCount = count(array_unique(array_column($rows, 'organization')));
    $allStudent = array_sum(array_column($rows, 'total_student'));

    date_default_timezone_set('Asia/Bangkok');
    $thaiMonths = [1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
    $thaiDate = date('j') . " " . $thaiMonths[(int) date('n')] . " " . (date('Y') + 543);

    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="th">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            body,
            table,
            th,
            td,
            h1 {
                font-family: "sarabun", sans-serif;
            }

            h1 {
                text-align: center;
                margin-bottom: 10px;
            }

            p {
                font-family: "sarabun", sans-serif;
                line-height: 0.75;
            }

            table {
                border-collapse: collapse;
                width: 100%;
                table-layout: fixed;
            }

            th,
            td {
                border: 1.15px solid #000;
                padding: 5px 2px;
                text-align: center;
                word-wrap: break-word;
            }

            th {
                background-color: #f2f2f2;
                font-weight: bold;
            }

            .text-left {
                text-align: left;
                padding: 0 0.3rem;
            }

            .text-center {
                text-align: center;
                padding: 0 0.3rem;
            }

            .report-header {
                width: 80%;
                margin: 0 auto 10px auto;
                border-collapse: collapse;
                text-align: left;
            }

            .report-header td {
                vertical-align: top;
                border: none;
                padding: 4px 10px;
                font-size: 14pt;
            }

            .report-header .left {
                width: 35%;
                white-space: nowrap;
            }

            .report-header .right {
                width: 15%;
                white-space: nowrap;
            }
        </style>
    </head>

    <body>
        <h1>รายงานประวัติการฝึกงาน</h1>
        <table class="report-header">
            <tr>
                <td class="left">
                    <p><b>วันที่พิมพ์รายงาน:</b> <?= $thaiDate; ?></p>
                    <p><b>ผลลัพธ์การค้นหา:</b> <?= count($rows); ?> การค้นหา</p>
                    <p><b>ตัวกรอง:</b>
                        <b>คณะ:</b> <?= htmlspecialchars($faculty ?: 'ทั้งหมด') ?>,
                        <b>หลักสูตร:</b> <?= htmlspecialchars($program ?: 'ทั้งหมด') ?>,
                        <b>สาขา:</b> <?= htmlspecialchars($major ?: 'ทั้งหมด') ?>,
                        <b>จังหวัด:</b> <?= htmlspecialchars($province ?: 'ทั้งหมด') ?>,
                        <b>ปีการศึกษา:</b> <?= htmlspecialchars($academicYear ?: 'ทั้งหมด') ?>
                    </p>
                </td>
                <td class="right">
                    <p><b>จำนวนบริษัท:</b> <?= $uniqueCompanyCount; ?> บริษัท</p>
                    <p><b>จำนวนนักศึกษา:</b> <?= $allStudent; ?> คน</p>
                </td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th width="40">ลำดับ</th>
                    <th>ชื่อบริษัท</th>
                    <th>จังหวัด</th>
                    <th>คณะ</th>
                    <th>หลักสูตร</th>
                    <th>สาขา</th>
                    <th width="60">ปีการศึกษา</th>
                    <th>สังกัด</th>
                    <th width="50">จำนวน (คน)</th>
                    <th width="50">MOU</th>
                    <th>ข้อมูลการติดต่อ</th>
                    <th width="50">คะแนน</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td class="text-left"><?= htmlspecialchars($row['organization']) ?></td>
                        <td class="text-left"><?= htmlspecialchars($row['province']) ?></td>
                        <td class="text-left"><?= htmlspecialchars($row['faculty']) ?></td>
                        <td class="text-left"><?= htmlspecialchars($row['program']) ?></td>
                        <td class="text-left"><?= htmlspecialchars($row['major']) ?></td>
                        <td><?= htmlspecialchars($row['year']) ?></td>
                        <td class="text-left"><?= htmlspecialchars($row['affiliation']) ?></td>
                        <td><?= htmlspecialchars($row['total_student']) ?></td>
                        <td><?= htmlspecialchars($row['mou_status']) ?></td>
                        <td class="text-left"><?= htmlspecialchars($row['contact']) ?></td>
                        <td><?= htmlspecialchars($row['score']) ?></td>
                    </tr>
                    <?php if (($index + 1) % 50 === 0)
                        echo "<!--CHUNK_BREAK-->"; ?>
                <?php endforeach ?>
            </tbody>
        </table>
    </body>

    </html>
    <?php
    $html = ob_get_clean();
    $chunks = explode('<!--CHUNK_BREAK-->', $html);
    foreach ($chunks as $chunk) {
        if (trim($chunk) !== '')
            $mpdf->WriteHTML($chunk);
    }
    $mpdf->Output('internship_report.pdf', 'I');
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
