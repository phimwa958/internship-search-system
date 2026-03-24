$(function () {
    const $section = $('#facultyTableSection');
    const baseUrl = $section.data('base-url');

    function escapeHtml(text) {
        return $('<div>').text(text == null ? '' : text).html();
    }

    const $addModal = $('#addModal');
    const $editModal = $('#editModal');
    const $deleteModal = $('#deleteModal');
    const $deleteLabel = $('#delete-major-label');

    let deleteId = null;

    const dt = $('#facultyTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        ajax: {
            url: './actions/fetch_faculty.php',
            type: 'POST'
        },
        columns: [{
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        {
            data: 'faculty',
            render: (v) => escapeHtml(v)
        },
        {
            data: 'program',
            render: (v) => escapeHtml(v)
        },
        {
            data: 'major',
            render: (v) => escapeHtml(v)
        },
        {
            data: null,
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
                return `
                    <div class="flex gap-2">
                        <button type="button" class="whitespace-nowrap btn-edit px-3 py-2 text-xs font-bold rounded-md bg-blue-600 hover:bg-blue-700 text-white"
                            data-id="${row.id}"
                            data-faculty="${escapeHtml(row.faculty)}"
                            data-program="${escapeHtml(row.program)}"
                            data-major="${escapeHtml(row.major)}"
                        >แก้ไข</button>

                        <button type="button" class="whitespace-nowrap btn-delete px-3 py-2 text-xs font-bold rounded-md bg-red-600 hover:bg-red-700 text-white"
                            data-id="${row.id}"
                            data-major="${escapeHtml(row.major)}"
                        >ลบ</button>
                    </div>
                `;
            }
        }
        ],
        createdRow: function (row, data) {
            $(row).attr('data-id', data.id);
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

    // Open add modal
    $('#openAddModal').on('click', function () {
        $('#addForm')[0].reset();
        $addModal.removeClass('hidden').addClass('flex');
    });

    // Close modals
    $('[data-close="add"]').on('click', function () {
        $addModal.addClass('hidden').removeClass('flex');
    });
    $('[data-close="edit"]').on('click', function () {
        $editModal.addClass('hidden').removeClass('flex');
    });
    $('[data-close="delete"]').on('click', function () {
        $deleteModal.addClass('hidden').removeClass('flex');
        deleteId = null;
        $deleteLabel.text('');
    });

    $('.fixed').on('click', function (e) {
        if (e.target === this) {
            $(this).addClass('hidden').removeClass('flex');
            if (this.id === 'deleteModal') {
                deleteId = null;
                $deleteLabel.text('');
            }
        }
    });

    // Submit add (AJAX)
    $('#addForm').on('submit', async function (e) {
        e.preventDefault();
        const form = this;
        const fd = new FormData(form);
        try {
            const resp = await fetch(form.action, {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await resp.json();
            if (!result.success) {
                alert(result.message || 'เกิดข้อผิดพลาด');
                return;
            }
            dt.ajax.reload(null, false);
            form.reset();
            $addModal.addClass('hidden').removeClass('flex');
        } catch (err) {
            console.error(err);
            alert('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้');
        }
    });

    // Open edit modal
    $(document).on('click', '.btn-edit', function () {
        const $btn = $(this);
        $('#edit-id').val($btn.data('id'));
        $('#edit-faculty').val($btn.data('faculty'));
        $('#edit-program').val($btn.data('program'));
        $('#edit-major').val($btn.data('major'));
        $editModal.removeClass('hidden').addClass('flex');
    });

    // Submit edit (AJAX)
    $('#editForm').on('submit', async function (e) {
        e.preventDefault();
        const form = this;
        const fd = new FormData(form);
        try {
            const resp = await fetch(form.action, {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await resp.json();
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

    // Open delete modal
    $(document).on('click', '.btn-delete', function () {
        const $btn = $(this);
        deleteId = $btn.data('id');
        $deleteLabel.text($btn.data('major') || '');
        $deleteModal.removeClass('hidden').addClass('flex');
    });

    // Confirm delete
    $('#confirmDeleteBtn').on('click', async function () {
        if (!deleteId) return;
        try {
            const fd = new FormData();
            fd.append('id', deleteId);
            const resp = await fetch('./actions/delete_faculty.php', {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await resp.json();
            if (!result.success) {
                alert(result.message || 'เกิดข้อผิดพลาด');
                return;
            }
            dt.ajax.reload(null, false);
            $deleteModal.addClass('hidden').removeClass('flex');
            deleteId = null;
            $deleteLabel.text('');
        } catch (err) {
            console.error(err);
            alert('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้');
        }
    });
});
