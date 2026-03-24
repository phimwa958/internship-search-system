<?php
require_once __DIR__ . '/../../includes/functions.php';
$baseUrl = $_ENV['BASE_URL'] ?? '';
?>
<section id="facultyTableSection" class="bg-gray-100" data-base-url="<?= htmlspecialchars($baseUrl) ?>">
    <section>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">ข้อมูลคณะในระบบ</h1>
        </div>

        <div class="bg-white shadow rounded-xl mb-6">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                <div class="flex items-center gap-2 text-gray-700">
                    <i class="fas fa-university"></i>
                    <span class="font-medium">รายการคณะ</span>
                </div>

                <button id="openAddModal" type="button"
                    class="inline-flex items-center px-2.5 py-1 text-sm font-medium rounded-md bg-emerald-600 hover:bg-emerald-700 text-white transition">
                    + เพิ่มสาขา
                </button>
            </div>

            <div class="px-4">
                <div class="overflow-x-auto no-scrollbar">
                    <table id="facultyTable" class="min-w-full text-sm text-left text-gray-700 w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-3 py-2 font-semibold">NO.</th>
                                <th class="px-3 py-2 font-semibold">คณะ</th>
                                <th class="px-3 py-2 font-semibold">หลักสูตร</th>
                                <th class="px-3 py-2 font-semibold">สาขา</th>
                                <th class="px-3 py-2 font-semibold text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Modal -->
    <div id="addModal" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center px-2">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-xl">
            <form id="addForm" action="./actions/add_faculty.php" method="post"
                class="flex flex-col max-h-[90vh] px-8 pt-6 pb-8">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold">เพิ่มคณะ / หลักสูตร / สาขา</h5>
                    <button type="button" data-close="add" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div class="pt-4 space-y-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">คณะ</label>
                        <input type="text" name="faculty" required
                            class="shadow border rounded-md w-full py-1.5 px-3 text-gray-700" />
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">หลักสูตร</label>
                        <input type="text" name="program" required
                            class="shadow border rounded-md w-full py-1.5 px-3 text-gray-700" />
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">สาขา</label>
                        <input type="text" name="major" required
                            class="shadow border rounded-md w-full py-1.5 px-3 text-gray-700" />
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 mt-4 border-t border-gray-200">
                    <button type="button" data-close="add"
                        class="px-3 py-1.5 text-sm font-bold rounded-md border border-gray-300 text-gray-700">ยกเลิก</button>
                    <button type="submit"
                        class="px-4 py-1.5 text-sm font-bold rounded-md bg-blue-500 hover:bg-blue-700 text-white">บันทึก</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center px-2">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-xl">
            <form id="editForm" action="./actions/edit_faculty.php" method="post"
                class="flex flex-col max-h-[90vh] px-8 pt-6 pb-8">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold">แก้ไขข้อมูล</h5>
                    <button type="button" data-close="edit" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div class="pt-4 space-y-4">
                    <input type="hidden" name="id" id="edit-id" />

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">คณะ</label>
                        <input type="text" name="faculty" id="edit-faculty" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" />
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">หลักสูตร</label>
                        <input type="text" name="program" id="edit-program" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" />
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">สาขา</label>
                        <input type="text" name="major" id="edit-major" required
                            class="shadow border rounded w-full py-2 px-3 text-gray-700" />
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 mt-4 border-t border-gray-200">
                    <button type="button" data-close="edit"
                        class="px-3 py-1.5 text-sm font-bold rounded-md border border-gray-300 text-gray-700">ยกเลิก</button>
                    <button type="submit" name="update"
                        class="px-4 py-1.5 text-sm font-bold rounded-md bg-blue-500 hover:bg-blue-700 text-white">อัปเดต</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center px-2">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="px-6 pt-6 pb-4 border-b border-gray-200 flex items-center justify-between">
                <h5 class="text-lg font-semibold text-red-600">ยืนยันการลบ</h5>
                <button type="button" data-close="delete" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <div class="px-6 py-4">
                <p class="text-sm text-gray-700">
                    ต้องการลบรายการนี้หรือไม่?
                    <br>
                    <span class="text-xs text-gray-500">สาขา: <span id="delete-major-label"
                            class="font-medium text-gray-800"></span></span>
                </p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" data-close="delete"
                    class="px-3 py-1.5 text-sm font-bold rounded-md border border-gray-300 text-gray-700">ยกเลิก</button>
                <button type="button" id="confirmDeleteBtn"
                    class="px-4 py-1.5 text-sm font-bold rounded-md bg-red-600 hover:bg-red-700 text-white">ลบ</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../public/js/dataTables.min.js"></script>
    <script src="../public/js/components/dashboard_faculty_table.js"></script>
</section>