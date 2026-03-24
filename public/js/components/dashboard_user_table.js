$(function () {
    const $section = $('#userTableSection');
    const baseUrl = $section.data('base-url');

    function escapeHtml(text) {
        return $('<div>').text(text == null ? '' : text).html();
    }

    const $addModal = $('#addUserModal');
    const $addForm = $('#addUserForm');
    const $editModal = $('#editUserModal');
    const $editForm = $('#editUserForm');
    const $deleteModal = $('#deleteUserModal');
    const $deleteEmailLabel = $('#delete-user-email-label');

    let deleteUserId = null;

    // Choices.js
    const addRoleSelect = document.getElementById('add-role');
    const editRoleSelect = document.getElementById('edit-role');
    let addRoleChoices = null;
    let editRoleChoices = null;

    if (addRoleSelect) {
        addRoleChoices = new Choices(addRoleSelect, {
            searchEnabled: true,
            itemSelectText: "",
        });
    }

    if (editRoleSelect) {
        editRoleChoices = new Choices(editRoleSelect, {
            searchEnabled: true,
            itemSelectText: "",
        });
    }

    const dt = $('#userTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        ajax: {
            url: './actions/fetch_users.php',
            type: 'POST'
        },
        columns: [{
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        }, {
            data: 'email',
            render: (v) => escapeHtml(v)
        }, {
            data: 'username',
            render: (v) => escapeHtml(v)
        }, {
            data: 'role',
            render: (v) => escapeHtml(v)
        }, {
            data: null,
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
                return `
                    <div class="flex gap-2">
                        <button type="button" class="whitespace-nowrap btn-edit-user inline-flex items-center px-3 py-2 text-xs font-bold rounded-md bg-blue-600 hover:bg-blue-700 text-white transition"
                            data-id="${row.id}"
                            data-email="${escapeHtml(row.email)}"
                            data-username="${escapeHtml(row.username)}"
                            data-role="${escapeHtml(row.role)}"
                        >แก้ไข</button>

                        <button type="button" class="whitespace-nowrap btn-delete-user inline-flex items-center px-3 py-2 text-xs font-bold rounded-md bg-red-600 hover:bg-red-700 text-white transition"
                            data-id="${row.id}"
                            data-email="${escapeHtml(row.email)}"
                        >ลบ</button>
                    </div>
                `;
            }
        }],
        createdRow: function (row, data) {
            $(row).attr('data-row-id', data.id);
        },
        language: {
            search: 'ค้นหา:',
            lengthMenu: 'แสดง _MENU_ แถวต่อหน้า',
            info: 'แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ แถว',
            infoEmpty: 'ไม่มีข้อมูล',
            infoFiltered: '(กรองจากทั้งหมด _MAX_ แถว)',
            zeroRecords: 'ไม่พบข้อมูลที่ค้นหา',
            paginate: {
                first: 'หน้าแรก',
                last: 'หน้าสุดท้าย',
                next: 'ถัดไป',
                previous: 'ก่อนหน้า'
            }
        }
    });

    // Add Modal
    $('#openAddUserModal').on('click', function () {
        $addForm[0].reset();
        if (addRoleChoices) addRoleChoices.setChoiceByValue('user');
        $addModal.removeClass('hidden').addClass('flex');
    });

    $('[data-close-modal="add-user"]').on('click', function () {
        $addModal.addClass('hidden').removeClass('flex');
    });

    // Close on backdrop
    $('.fixed').on('click', function (e) {
        if (e.target === this) {
            $(this).addClass('hidden').removeClass('flex');
            if (this.id === 'deleteUserModal') {
                deleteUserId = null;
                $deleteEmailLabel.text('');
            }
        }
    });

    // Submit add (AJAX)
    $addForm.on('submit', async function (e) {
        e.preventDefault();
        const form = this;
        const fd = new FormData(form);
        try {
            const res = await fetch(form.action, {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await res.json();
            if (!result.success) {
                alert(result.message || 'เกิดข้อผิดพลาด');
                return;
            }
            dt.ajax.reload(null, false);
            form.reset();
            if (addRoleChoices) addRoleChoices.setChoiceByValue('user');
            $addModal.addClass('hidden').removeClass('flex');
        } catch (err) {
            console.error(err);
            alert('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้');
        }
    });

    // Edit Modal
    $(document).on('click', '.btn-edit-user', function () {
        const $btn = $(this);
        $('#edit-id').val($btn.data('id'));
        $('#edit-email').val($btn.data('email'));
        $('#edit-username').val($btn.data('username'));
        $('#edit-password').val('');

        const role = $btn.data('role') || 'user';
        if (editRoleChoices) {
            editRoleChoices.setChoiceByValue(role);
        } else {
            $('#edit-role').val(role);
        }
        $editModal.removeClass('hidden').addClass('flex');
    });

    $('[data-close-modal="edit-user"]').on('click', function () {
        $editModal.addClass('hidden').removeClass('flex');
    });

    // Submit edit (AJAX)
    $editForm.on('submit', async function (e) {
        e.preventDefault();
        const form = this;
        const fd = new FormData(form);
        try {
            const res = await fetch(form.action, {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await res.json();
            if (!result.success) {
                alert(result.message || 'เกิดข้อผิดพลาด');
                return;
            }
            dt.ajax.reload(null, false);
            $editModal.addClass('hidden').removeClass('flex');
        } catch (err) {
            console.error(err);
            alert('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้');
        }
    });

    // Delete Modal
    $(document).on('click', '.btn-delete-user', function () {
        const id = $(this).data('id');
        const email = $(this).data('email');
        if (!id) return;
        deleteUserId = id;
        $deleteEmailLabel.text(email || '');
        $deleteModal.removeClass('hidden').addClass('flex');
    });

    $('[data-close-modal="delete-user"]').on('click', function () {
        $deleteModal.addClass('hidden').removeClass('flex');
        deleteUserId = null;
        $deleteEmailLabel.text('');
    });

    $('#confirmDeleteUserBtn').on('click', async function () {
        if (!deleteUserId) return;
        const fd = new FormData();
        fd.append('id', deleteUserId);
        try {
            const res = await fetch('./actions/delete_user.php', {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await res.json();
            if (!result.success) {
                alert(result.message || 'เกิดข้อผิดพลาด');
                return;
            }
            $deleteModal.addClass('hidden').removeClass('flex');
            deleteUserId = null;
            $deleteEmailLabel.text('');
            dt.ajax.reload(null, false);
        } catch (err) {
            console.error(err);
            alert('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้');
        }
    });
});
