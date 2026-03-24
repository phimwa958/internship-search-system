<?php
require_once __DIR__ . '/../../includes/functions.php';
$baseUrl = $_ENV['BASE_URL'] ?? '';
?>
<link rel="stylesheet" href="../public/css/choices.min.css" />

<section id="userTableSection" data-base-url="<?= htmlspecialchars($baseUrl) ?>">
    <!-- ตารางข้อมูลผู้ใช้ -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            ข้อมูลผู้ใช้ระบบ
        </h1>
    </div>

    <div class="bg-white shadow rounded-xl mb-6">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <div class="flex items-center gap-2 text-gray-700">
                <i class="fas fa-users"></i>
                <span class="font-medium">รายการผู้ใช้</span>
            </div>

            <!-- ปุ่มเปิด Add Modal -->
            <button id="openAddUserModal" type="button"
                class="inline-flex items-center px-2.5 py-1 text-sm font-medium rounded-md bg-emerald-600 hover:bg-emerald-700 text-white transition">
                + เพิ่มผู้ใช้
            </button>
        </div>

        <div class="px-4">
            <div class="overflow-x-auto no-scrollbar">
                <table id="userTable" class="min-w-full text-sm text-left text-gray-700 w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2 font-semibold">NO.</th>
                            <th class="px-3 py-2 font-semibold">อีเมล</th>
                            <th class="px-3 py-2 font-semibold">ชื่อผู้ใช้</th>
                            <th class="px-3 py-2 font-semibold">สิทธิ์</th>
                            <th class="px-3 py-2 font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center px-2">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-xl">
            <form method="post" action="./actions/add_user.php" id="addUserForm"
                class="flex flex-col max-h-[90vh] bg-white shadow-md rounded px-8 pt-6 pb-8">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold">เพิ่มผู้ใช้</h5>
                    <button type="button" data-close-modal="add-user"
                        class="text-gray-400 hover:text-gray-600 transition">
                        <span class="text-xl leading-none">&times;</span>
                    </button>
                </div>

                <div class="pt-4 space-y-4 overflow-y-auto">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">อีเมล</label>
                        <input type="email" name="email"
                            class="shadow appearance-none border rounded-md w-full py-1.5 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">ชื่อผู้ใช้</label>
                        <input type="text" name="username"
                            class="shadow appearance-none border rounded-md w-full py-1.5 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">รหัสผ่าน</label>
                        <input type="password" name="password"
                            class="shadow appearance-none border rounded-md w-full py-1.5 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">สิทธิ์</label>
                        <select name="role" id="add-role"
                            class="shadow appearance-none border rounded-md w-full py-1.5 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                            <option value="user" selected>user</option>
                            <option value="admin">admin</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 mt-4 border-t border-gray-200">
                    <button type="button" data-close-modal="add-user"
                        class="px-3 py-1.5 text-sm font-bold rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">ยกเลิก</button>
                    <button type="submit"
                        class="px-4 py-1.5 text-sm font-bold rounded-md bg-blue-500 hover:bg-blue-700 text-white">บันทึก</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center px-2">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-xl">
            <form method="post" action="./actions/edit_user.php"
                class="flex flex-col max-h-[90vh] bg-white shadow-md rounded px-8 pt-6 pb-8" id="editUserForm">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold">แก้ไขผู้ใช้</h5>
                    <button type="button" data-close-modal="edit-user"
                        class="text-gray-400 hover:text-gray-600 transition">
                        <span class="text-xl leading-none">&times;</span>
                    </button>
                </div>

                <div class="pt-4 space-y-4 overflow-y-auto">
                    <input type="hidden" name="id" id="edit-id">

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">อีเมล</label>
                        <input type="email" name="email" id="edit-email"
                            class="shadow appearance-none border rounded-md w-full py-1.5 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">ชื่อผู้ใช้</label>
                        <input type="text" name="username" id="edit-username"
                            class="shadow appearance-none border rounded-md w-full py-1.5 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">รหัสผ่านใหม่
                            (ถ้าไม่เปลี่ยนให้เว้นว่าง)</label>
                        <input type="password" name="password" id="edit-password"
                            class="shadow appearance-none border rounded-md w-full py-1.5 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1.5">สิทธิ์</label>
                        <select name="role" id="edit-role"
                            class="shadow appearance-none border rounded-md w-full py-1.5 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                            <option value="user">user</option>
                            <option value="admin">admin</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 mt-4 border-t border-gray-200">
                    <button type="button" data-close-modal="edit-user"
                        class="px-3 py-1.5 text-sm font-bold rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">ยกเลิก</button>
                    <button type="submit" name="update"
                        class="px-4 py-1.5 text-sm font-bold rounded-md bg-blue-500 hover:bg-blue-700 text-white">อัปเดต</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div id="deleteUserModal" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center px-2">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="px-6 pt-6 pb-4 border-b border-gray-200 flex items-center justify-between">
                <h5 class="text-lg font-semibold text-red-600">ยืนยันการลบผู้ใช้</h5>
                <button type="button" data-close-modal="delete-user"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <span class="text-xl leading-none">&times;</span>
                </button>
            </div>

            <div class="px-6 py-4">
                <p class="text-sm text-gray-700">
                    คุณต้องการลบผู้ใช้ระบบนี้หรือไม่?
                    <br>
                    <span class="text-xs text-gray-500">อีเมล: <span id="delete-user-email-label"
                            class="font-medium text-gray-800"></span></span>
                    <br>
                    <span class="text-xs text-gray-500">เมื่อยืนยันแล้วจะไม่สามารถกู้คืนได้</span>
                </p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" data-close-modal="delete-user"
                    class="px-3 py-1.5 text-sm font-bold rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">ยกเลิก</button>
                <button type="button" id="confirmDeleteUserBtn"
                    class="px-4 py-1.5 text-sm font-bold rounded-md bg-red-600 hover:bg-red-700 text-white">ลบผู้ใช้</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../public/js/dataTables.min.js"></script>
    <script src="../public/js/choices.min.js"></script>
    <script src="../public/js/components/dashboard_user_table.js"></script>
</section>