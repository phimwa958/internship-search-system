<?php
require_once __DIR__ . '/../../includes/functions.php';
$baseUrl = $_ENV['BASE_URL'] ?? '';
?>

<section id="feedbackTableSection" data-base-url="<?= htmlspecialchars($baseUrl) ?>">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">ข้อมูล Feedback จากผู้ใช้</h1>
    </div>

    <div class="bg-white shadow rounded-xl mb-6">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <div class="flex items-center gap-2 text-gray-700">
                <i class="fas fa-comment-dots"></i>
                <span class="font-medium">รายการ Feedback</span>
            </div>
        </div>

        <div class="px-4">
            <div class="overflow-x-auto no-scrollbar">
                <table id="feedbackTable" class="min-w-full text-sm text-left text-gray-700 w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2 font-semibold">NO.</th>
                            <th class="px-3 py-2 font-semibold">มีประโยชน์หรือไม่</th>
                            <th class="px-3 py-2 font-semibold">ความคิดเห็น</th>
                            <th class="px-3 py-2 font-semibold">วันที่ส่ง</th>
                            <th class="px-3 py-2 font-semibold text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center px-2">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="px-6 pt-6 pb-4 border-b border-gray-200 flex items-center justify-between">
                <h5 class="text-lg font-semibold text-red-600">ยืนยันการลบ Feedback</h5>
                <button type="button" data-close="delete" class="text-gray-400 hover:text-gray-600 transition">
                    <span class="text-xl leading-none">&times;</span>
                </button>
            </div>

            <div class="px-6 py-4">
                <p class="text-sm text-gray-700">
                    คุณต้องการลบ Feedback นี้หรือไม่?
                    <br>
                    <span class="text-xs text-gray-500">ความคิดเห็น: <span id="delete-feedback-label"
                            class="font-medium"></span></span>
                    <br>
                    <span class="text-xs text-gray-500">เมื่อยืนยันแล้วจะไม่สามารถกู้คืนได้</span>
                </p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" data-close="delete"
                    class="px-4 py-2 text-sm font-bold rounded border border-gray-300 text-gray-700 hover:bg-gray-50">ยกเลิก</button>
                <button type="button" id="confirmDeleteBtn"
                    class="px-4 py-2 text-sm font-bold rounded bg-red-600 hover:bg-red-700 text-white transition">ลบข้อมูล</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../public/js/dataTables.min.js"></script>
    <script src="../public/js/components/dashboard_feedback_table.js"></script>
</section>