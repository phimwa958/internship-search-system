document.addEventListener('DOMContentLoaded', () => {
    function escapeHtml(text) {
        return $('<div>').text(text == null ? '' : text).html();
    }

    const table = new DataTable('#myTable', {
        serverSide: true,
        processing: true,
        ajax: {
            url: './actions/fetch_internships.php',
            type: 'POST',
            data: (data) => {
                data.faculty = document.getElementById('faculty')?.value || '';
                data.program = document.getElementById('program')?.value || '';
                data.major = document.getElementById('major')?.value || '';
                data.province = document.getElementById('province')?.value || '';
                data['academic-year'] = document.getElementById('academic-year')?.value || '';
            },
            error: (xhr) => {
                console.error('DT Ajax error:', xhr.status, xhr.responseText);
                alert(`โหลดข้อมูลไม่สำเร็จ (${xhr.status})`);
            },
            dataSrc: (json) => json?.data ?? [],
        },
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, 'ทั้งหมด'],
        ],
        pageLength: 10,
        columns: [
            {
                data: null,
                className: '!text-center',
                orderable: false,
                searchable: false,
                render: (data, type, row, meta) => meta.row + 1 + meta.settings._iDisplayStart,
            },
            { data: 'organization', className: 'text-left p-2', render: (v) => escapeHtml(v) },
            { data: 'province', className: 'text-left p-2', render: (v) => escapeHtml(v) },
            { data: 'faculty', className: 'text-left p-2', render: (v) => escapeHtml(v) },
            { data: 'program', className: 'text-left p-2', render: (v) => escapeHtml(v) },
            { data: 'major', className: 'text-left p-2', render: (v) => escapeHtml(v) },
            { data: 'year', className: '!text-center p-2' },
            { data: 'affiliation', className: '!text-center p-2', render: (v) => escapeHtml(v) },
            { data: 'total_student', className: '!text-center p-2' },
            { data: 'mou_status', className: 'text-center p-2', render: (v) => escapeHtml(v) },
        ],
        language: {
            search: 'ค้นหา:',
            lengthMenu: 'แสดง _MENU_ แถว',
            info: 'แสดง _START_–_END_ จาก _TOTAL_ แถว',
            infoEmpty: 'ไม่มีข้อมูลให้แสดง',
            zeroRecords: 'ไม่พบรายการที่ค้นหา',
            paginate: {
                first: 'หน้าแรก',
                previous: 'ก่อนหน้า',
                next: 'ถัดไป',
                last: 'หน้าสุดท้าย',
            },
        },
    });

    const filterForm = document.getElementById('filter-form');
    if (filterForm) {
        filterForm.addEventListener('submit', (event) => {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            const newUrl = `${window.location.pathname}?${params.toString()}`;
            window.history.replaceState({}, '', newUrl);

            if (typeof table !== 'undefined') {
                table.ajax.reload();
            }
        });
    }
});
