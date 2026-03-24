$(function () {
    const $section = $('#feedbackTableSection');
    const baseUrl = $section.data('base-url');

    function escapeHtml(text) {
        return $('<div>').text(text == null ? '' : text).html();
    }

    const $deleteModal = $('#deleteModal');
    const $deleteLabel = $('#delete-feedback-label');
    let deleteId = null;

    const dt = $('#feedbackTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        ajax: {
            url: './actions/fetch_feedback.php',
            type: 'POST'
        },
        columns: [{
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        }, {
            data: 'is_useful',
            render: function (data) {
                return escapeHtml(data);
            }
        }, {
            data: 'comment',
            render: function (data) {
                return escapeHtml(data);
            }
        }, {
            data: 'created_at',
            render: function (data) {
                return escapeHtml(data);
            }
        }, {
            data: null,
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
                return `
                    <div class="flex justify-center">
                        <button type="button" class="btn-delete px-3 py-2 text-xs font-bold rounded-md bg-red-600 hover:bg-red-700 text-white transition"
                            data-id="${row.id}"
                            data-comment="${escapeHtml(row.comment)}"
                        >ลบ</button>
                    </div>
                `;
            }
        }],
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

    // Delete Modal
    $(document).on('click', '.btn-delete', function () {
        const $btn = $(this);
        deleteId = $btn.data('id');
        const comment = $btn.data('comment') || '';
        $deleteLabel.text(comment.length > 50 ? comment.substring(0, 50) + '…' : comment);
        $deleteModal.removeClass('hidden').addClass('flex');
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

    $('#confirmDeleteBtn').on('click', async function () {
        if (!deleteId) return;
        const fd = new FormData();
        fd.append('id', deleteId);
        try {
            const res = await fetch('./actions/delete_feedback.php', {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await res.json();
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
