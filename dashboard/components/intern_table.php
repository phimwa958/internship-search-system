<?php
require_once __DIR__ . '/../../includes/functions.php';

$baseUrl = $_ENV['BASE_URL'] ?? '';

// ดึงข้อมูลคณะ หลักสูตรสาขา
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

<!-- Datatables CSS -->
<link rel="stylesheet" href="../public/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="../public/css/buttons.dataTables.min.css">

<!-- Choices.js CSS -->
<link rel="stylesheet" href="../public/css/choices.min.css" />

<section id="internshipTableSection" class="bg-gray-100"
    data-faculty-map='<?= json_encode($facultyMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>'
    data-base-url="<?= htmlspecialchars($baseUrl) ?>">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">ข้อมูลสถานที่ฝึกงาน</h1>
    </div>

    <div class="bg-white shadow rounded-xl mb-6">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <div class="flex items-center gap-2 text-gray-700">
                <i class="fas fa-briefcase"></i>
                <span class="font-medium">ข้อมูลการฝึกงาน</span>
            </div>

            <button id="openAddInternshipModal" type="button"
                class="inline-flex items-center px-2.5 py-1 text-sm font-medium rounded-md bg-emerald-600 hover:bg-emerald-700 text-white transition">
                + เพิ่มข้อมูลฝึกงาน
            </button>
        </div>

        <div class="px-4 py-4">
            <div class="overflow-x-auto no-scrollbar">
                <table id="internshipTable" class="min-w-full text-sm text-left text-gray-700 w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2 font-semibold">NO.</th>
                            <th class="px-3 py-2 font-semibold">หน่วยงาน</th>
                            <th class="px-3 py-2 font-semibold">จังหวัด</th>
                            <th class="px-3 py-2 font-semibold">คณะ / โรงเรียน</th>
                            <th class="px-3 py-2 font-semibold">หลักสูตร</th>
                            <th class="px-3 py-2 font-semibold">สาขาวิชา</th>
                            <th class="px-3 py-2 font-semibold">ปีการศึกษา</th>
                            <th class="px-3 py-2 font-semibold">สังกัด</th>
                            <th class="px-3 py-2 font-semibold">จำนวนที่รับ</th>
                            <th class="px-3 py-2 font-semibold">MOU</th>
                            <th class="px-3 py-2 font-semibold">ข้อมูลการติดต่อ</th>
                            <th class="px-3 py-2 font-semibold text-center">คะแนน</th>
                            <th class="px-3 py-2 font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Internship Modal -->
    <div id="addInternshipModal" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center px-2">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <form id="addForm" action="./actions/add_internship.php" method="post" class="p-5">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-4">
                    <h5 class="text-lg font-semibold text-gray-800">เพิ่มข้อมูลสถานที่ฝึกงาน</h5>
                    <button type="button" data-close-modal="add" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">ชื่อหน่วยงาน / บริษัท</label>
                        <input type="text" name="organization" required class="w-full border rounded-md px-3 py-1.5 focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">จังหวัด</label>
                        <select name="province" id="add-province" required></select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">สังกัด</label>
                        <select name="affiliation" id="add-affiliation" required></select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">คณะ / โรงเรียน</label>
                        <select name="faculty" id="add-faculty" required></select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">หลักสูตร</label>
                        <select name="program" id="add-program" required></select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">สาขาวิชา</label>
                        <select name="major" id="add-major" required></select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">ปีการศึกษา</label>
                        <input type="number" name="year" id="add-year" required class="w-full border rounded-md px-3 py-1.5 focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">จำนวนที่รับ (คน)</label>
                        <input type="number" name="total_student" id="add-total_student" required class="w-full border rounded-md px-3 py-1.5 focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">สถานะ MOU</label>
                        <select name="mou_status" id="add-mou" required></select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">คะแนน (0-5)</label>
                        <input type="number" name="score" step="0.1" min="0" max="5" class="w-full border rounded-md px-3 py-1.5 focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">ข้อมูลการติดต่อ / รายละเอียด</label>
                        <textarea name="contact" rows="3" class="w-full border rounded-md px-3 py-1.5 focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" data-close-modal="add" class="px-3 py-1.5 text-sm text-gray-600 font-medium hover:bg-gray-100 rounded-md transition">ยกเลิก</button>
                    <button type="submit" class="px-4 py-1.5 text-sm bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-md transition shadow-md">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Internship Modal -->
    <div id="editInternshipModal" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center px-2">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <form id="editForm" action="./actions/edit_internship.php" method="post" class="p-5">
                <input type="hidden" name="id" id="edit-id">
                
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-4">
                    <h5 class="text-lg font-semibold text-gray-800">แก้ไขข้อมูลสถานที่ฝึกงาน</h5>
                    <button type="button" data-close-modal="edit" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">ชื่อหน่วยงาน / บริษัท</label>
                        <input type="text" name="organization" id="edit-organization" required class="w-full border rounded-md px-3 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">จังหวัด</label>
                        <select name="province" id="edit-province" required></select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">สังกัด</label>
                        <select name="affiliation" id="edit-affiliation" required></select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">คณะ / โรงเรียน</label>
                        <select name="faculty" id="edit-faculty" required></select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">หลักสูตร</label>
                        <select name="program" id="edit-program" required></select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">สาขาวิชา</label>
                        <select name="major" id="edit-major" required></select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">ปีการศึกษา</label>
                        <input type="number" name="year" id="edit-year" required class="w-full border rounded-md px-3 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">จำนวนที่รับ (คน)</label>
                        <input type="number" name="total_student" id="edit-total_student" required class="w-full border rounded-md px-3 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">สถานะ MOU</label>
                        <select name="mou_status" id="edit-mou" required></select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">คะแนน (0-5)</label>
                        <input type="number" name="score" id="edit-score" step="0.1" min="0" max="5" class="w-full border rounded-md px-3 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">ข้อมูลการติดต่อ / รายละเอียด</label>
                        <textarea name="contact" id="edit-contact" rows="3" class="w-full border rounded-md px-3 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" data-close-modal="edit" class="px-3 py-1.5 text-sm text-gray-600 font-medium hover:bg-gray-100 rounded-md transition">ยกเลิก</button>
                    <button type="submit" class="px-4 py-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition shadow-md">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center px-2">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">ยืนยันการลบข้อมูล</h3>
                <p class="text-gray-600 mb-6">คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลสถานที่ฝึกงานนี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้</p>
                
                <div class="flex justify-center gap-3">
                    <button type="button" data-close-modal="delete" class="px-3 py-1.5 text-sm text-gray-600 font-medium hover:bg-gray-100 rounded-md transition">ยกเลิก</button>
                    <button id="confirmDeleteBtn" type="button" class="px-4 py-1.5 text-sm bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition shadow-md">ยืนยันการลบ</button>
                </div>
            </div>
        </div>
    </div>

</section>

<!-- Datatables & Components JS -->
<script src="../public/js/jquery-3.7.1.min.js"></script>
<script src="../public/js/dataTables.min.js"></script>
<script src="../public/js/dataTables.buttons.min.js"></script>
<script src="../public/js/buttons.colVis.min.js"></script>
<script src="../public/js/choices.min.js"></script>
<script src="../public/js/components/dashboard_intern_table.js" defer></script>