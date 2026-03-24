<?php
require_once __DIR__ . '/../includes/functions.php';

$faculty = $_GET['faculty'] ?? '';
$program = $_GET['program'] ?? '';
$major = $_GET['major'] ?? '';
$province = $_GET['province'] ?? '';
$academicYear = $_GET['academic-year'] ?? '';

// Fetch available years
$yearsArray = db_fetch_column("SELECT DISTINCT year FROM internship_stats");

// Build faculty/major/program map
$facultyMap = [];
try {
    $rows = db_fetch_all("
        SELECT faculty, program, major
        FROM faculty_program_major
        ORDER BY faculty, major, program
    ");

    foreach ($rows as $row) {
        $fName = $row['faculty'];
        $mName = $row['major'];
        $pName = $row['program'];

        if (!isset($facultyMap[$fName])) {
            $facultyMap[$fName] = [];
        }
        $facultyMap[$fName][$mName] = $pName;
    }
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage();
}
?>

<section class="mx-auto max-w-7xl px-4 mt-10">
    <form id="filter-form" action="index.php" method="GET"
        data-faculty-map='<?= htmlspecialchars(json_encode($facultyMap, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>'
        data-selected-faculty="<?= htmlspecialchars($faculty); ?>"
        data-selected-major="<?= htmlspecialchars($major); ?>"
        data-selected-program="<?= htmlspecialchars($program); ?>"
        data-selected-province="<?= htmlspecialchars($province); ?>"
        data-selected-academic-year="<?= htmlspecialchars($academicYear); ?>"
        data-years-array='<?= json_encode($yearsArray); ?>'>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="faculty" class="block mb-2 font-medium">คณะ</label>
                <select id="faculty" name="faculty" class="w-full mb-4 border rounded-md px-3 py-2">
                    <option value="">-เลือกคณะ-</option>
                </select>
            </div>

            <div>
                <label for="program" class="block mb-2 font-medium">หลักสูตร</label>
                <select id="program" name="program" class="w-full border rounded-md px-3 py-2">
                    <option value="">-เลือกหลักสูตร-</option>
                </select>
            </div>

            <div>
                <label for="major" class="block mb-2 font-medium">สาขา</label>
                <select id="major" name="major" class="w-full border rounded-md px-3 py-2">
                    <option value="">-เลือกสาขา-</option>
                </select>
            </div>

            <div>
                <label for="province" class="block mb-2 font-medium">จังหวัด</label>
                <select id="province" name="province" class="w-full border rounded-md px-3 py-2">
                    <option value="">-เลือกจังหวัด-</option>
                </select>
            </div>

            <div>
                <label for="academic-year" class="block mb-2 font-medium">ปีการศึกษา</label>
                <select id="academic-year" name="academic-year" class="w-full border rounded-md px-3 py-2">
                    <option value="">-เลือก พ.ศ.-</option>
                </select>
            </div>
        </div>

        <div class="mt-4 mb-3 flex items-center justify-center gap-3">
            <button
                class="inline-flex items-center justify-center h-11 px-5 rounded-md bg-sky-500 hover:bg-sky-600 text-white font-bold"
                type="submit">
                ค้นหา
            </button>
            <button id="clear-search-query"
                class="inline-flex items-center justify-center h-11 px-5 rounded-md bg-gray-200 hover:bg-gray-300"
                type="button">
                ล้างการค้นหา
            </button>
        </div>
    </form>
</section>

<script src="./public/js/components/filter_search.js" defer></script>