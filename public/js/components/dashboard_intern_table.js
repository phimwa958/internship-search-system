/**
 * Internship Table Dashboard Logic
 */

// Escape HTML when put the string to the data-... or innerHTML
function escapeHtml(text) {
    return $('<div>').text(text == null ? '' : text).html();
}

const provinces = [
    "กรุงเทพมหานคร", "กระบี่", "กาญจนบุรี", "กาฬสินธุ์", "กำแพงเพชร",
    "ขอนแก่น", "จันทบุรี", "ฉะเชิงเทรา", "ชลบุรี", "ชัยนาท", "ชัยภูมิ",
    "ชุมพร", "เชียงราย", "เชียงใหม่", "ตรัง", "ตราด", "ตาก", "นครนายก",
    "นครปฐม", "นครพนม", "นครราชสีมา", "นครศรีธรรมราช", "นครสวรรค์",
    "นนทบุรี", "นราธิวาส", "น่าน", "บึงกาฬ", "บุรีรัมย์", "ปทุมธานี",
    "ประจวบคีรีขันธ์", "ปราจีนบุรี", "ปัตตานี", "พระนครศรีอยุธยา",
    "พะเยา", "พังงา", "พัทลุง", "พิจิตร", "พิษณุโลก", "เพชรบุรี",
    "เพชรบูรณ์", "แพร่", "ภูเก็ต", "มหาสารคาม", "มุกดาหาร", "แม่ฮ่องสอน",
    "ยโสธร", "ยะลา", "ร้อยเอ็ด", "ระนอง", "ระยอง", "ราชบุรี", "ลพบุรี",
    "ลำปาง", "ลำพูน", "เลย", "ศรีสะเกษ", "สกลนคร", "สงขลา", "สตูล",
    "สมุทรปราการ", "สมุทรสงคราม", "สมุทรสาคร", "สระแก้ว", "สระบุรี",
    "สิงห์บุรี", "สุโขทัย", "สุพรรณบุรี", "สุราษฎร์ธานี", "สุรินทร์",
    "หนองคาย", "หนองบัวลำภู", "อ่างทอง", "อำนาจเจริญ", "อุดรธานี",
    "อุตรดิตถ์", "อุทัยธานี", "อุบลราชธานี"
];

const mouStatusOptions = ["มี", "ไม่มี", "ไม่ระบุ"];
const affiliationOptions = ["ภาครัฐ", "ภาคเอกชน", "รัฐวิสาหกิจ", "ไม่มี"];

const sortChoice = (a, b) => {
    if (a.value === '' && b.value !== '') return -1;
    if (a.value !== '' && b.value === '') return 1;
    return a.label.localeCompare(b.label, 'th');
};

function setupDropdownGroup(prefix, facultyMajorsPrograms) {
    const facultySelect = document.getElementById(prefix + '-faculty');
    const majorSelect = document.getElementById(prefix + '-major');
    const programSelect = document.getElementById(prefix + '-program');
    const provinceSelect = document.getElementById(prefix + '-province');
    const mouSelect = document.getElementById(prefix + '-mou');
    const affiliationSelect = document.getElementById(prefix + '-affiliation');

    if (!facultySelect || !majorSelect || !programSelect || !provinceSelect || !mouSelect || !affiliationSelect) {
        return null;
    }

    const allFaculties = Object.keys(facultyMajorsPrograms);
    const allMajors = Object.values(facultyMajorsPrograms).flatMap(obj => Object.keys(obj));
    const allPrograms = [...new Set(Object.values(facultyMajorsPrograms).flatMap(obj => Object.values(obj)))];

    const facultyChoices = new Choices(facultySelect, { searchEnabled: true, itemSelectText: "", sorter: sortChoice });
    const majorChoices = new Choices(majorSelect, { searchEnabled: true, itemSelectText: "", sorter: sortChoice });
    const programChoices = new Choices(programSelect, { searchEnabled: true, itemSelectText: "", sorter: sortChoice });
    const provinceChoices = new Choices(provinceSelect, { searchEnabled: true, itemSelectText: "", sorter: sortChoice });
    const mouChoices = new Choices(mouSelect, { searchEnabled: true, itemSelectText: "" });
    const affiliationChoices = new Choices(affiliationSelect, { searchEnabled: true, itemSelectText: "" });

    const populate = (choiceInstance, list, placeholder) => {
        choiceInstance.clearStore();
        choiceInstance.setChoices(
            [{ value: "", label: placeholder, selected: true }].concat(list.map(v => ({ value: v, label: v }))),
            "value", "label", true
        );
    };

    populate(facultyChoices, allFaculties, "-เลือกคณะ-");
    populate(majorChoices, allMajors, "-เลือกสาขา-");
    populate(programChoices, allPrograms, "-เลือกหลักสูตร-");
    populate(provinceChoices, provinces, "-เลือกจังหวัด-");
    populate(mouChoices, mouStatusOptions, "-เลือกสถานะ MOU-");
    populate(affiliationChoices, affiliationOptions, "-เลือกสังกัด-");

    facultySelect.addEventListener('change', () => {
        const fac = facultySelect.value;
        const majors = fac ? Object.keys(facultyMajorsPrograms[fac]) : allMajors;
        const progs = fac ? [...new Set(Object.values(facultyMajorsPrograms[fac]))] : allPrograms;
        populate(majorChoices, majors, "-เลือกสาขา-");
        populate(programChoices, progs, "-เลือกหลักสูตร-");
    });

    majorSelect.addEventListener('change', () => {
        const major = majorSelect.value;
        if (!major) return;
        for (const [fac, obj] of Object.entries(facultyMajorsPrograms)) {
            if (major in obj) {
                facultyChoices.setChoiceByValue(fac);
                populate(majorChoices, Object.keys(obj), "-เลือกสาขา-");
                populate(programChoices, [...new Set(Object.values(obj))], "-เลือกหลักสูตร-");
                majorChoices.setChoiceByValue(major);
                programChoices.setChoiceByValue(obj[major]);
                break;
            }
        }
    });

    return {
        facultyChoices, majorChoices, programChoices, provinceChoices, mouChoices, affiliationChoices,
        resetValues: () => {
            [facultyChoices, majorChoices, programChoices, provinceChoices, mouChoices, affiliationChoices].forEach(c => c.setChoiceByValue(''));
        }
    };
}

$(function () {
    const root = document.getElementById('internshipTableSection');
    if (!root) return;
    const facultyMap = JSON.parse(root.getAttribute('data-faculty-map') || '{}');

    const addDropdowns = setupDropdownGroup('add', facultyMap);
    const editDropdowns = setupDropdownGroup('edit', facultyMap);

    const dt = $('#internshipTable').DataTable({
        processing: true,
        serverSide: true,
        dom: 'lBfrtip',
        buttons: [{ extend: 'colvis', text: 'เลือกคอลัมน์', className: 'bg-gray-200 border border-gray-300 rounded px-3 py-1 text-sm' }],
        ajax: { url: './actions/fetch_internships.php', type: 'POST' },
        columnDefs: [{ targets: [9, 12], visible: false }],
        columns: [
            { data: null, render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1 },
            { data: 'organization', render: (v) => escapeHtml(v) },
            { data: 'province', render: (v) => escapeHtml(v) },
            { data: 'faculty', render: (v) => escapeHtml(v) },
            { data: 'program', render: (v) => escapeHtml(v) },
            { data: 'major', render: (v) => escapeHtml(v) },
            { data: 'year' },
            { data: 'affiliation', render: (v) => escapeHtml(v) },
            { data: 'total_student' },
            { data: 'mou_status', render: (v) => escapeHtml(v) },
            { data: 'contact', render: (v) => escapeHtml(v) },
            { data: 'score' },
            { data: 'created_at' },
            {
                data: null, orderable: false, searchable: false,
                render: (d, t, r) => `
                    <div class="flex gap-2">
                        <button type="button" class="btn-edit px-3 py-2 text-xs font-bold rounded-md bg-blue-600 text-white" 
                            data-id="${r.id}" data-organization="${escapeHtml(r.organization)}" data-province="${escapeHtml(r.province)}"
                            data-faculty="${escapeHtml(r.faculty)}" data-program="${escapeHtml(r.program)}" data-major="${escapeHtml(r.major)}"
                            data-year="${escapeHtml(r.year)}" data-total_student="${escapeHtml(r.total_student)}" data-mou_status="${escapeHtml(r.mou_status)}"
                            data-affiliation="${escapeHtml(r.affiliation)}" data-contact="${escapeHtml(r.contact)}" data-score="${escapeHtml(r.score)}">แก้ไข</button>
                        <button type="button" class="btn-delete px-3 py-2 text-xs font-bold rounded-md bg-red-600 text-white" data-id="${r.id}">ลบ</button>
                    </div>`
            }
        ],
        createdRow: (row, data) => {
            const cells = $(row).find('td');
            $(cells[9]).addClass('cell-contact');
        },
        language: {
            search: 'ค้นหา:', lengthMenu: 'แสดง _MENU_ แถว', info: 'แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว',
            paginate: { first: 'หน้าแรก', last: 'หน้าสุดท้าย', next: 'ถัดไป', previous: 'ก่อนหน้า' }
        }
    });

    // Modals
    const modals = {
        add: $('#addInternshipModal'),
        edit: $('#editInternshipModal'),
        delete: $('#deleteConfirmModal')
    };

    $('#openAddInternshipModal').on('click', () => {
        addDropdowns?.resetValues();
        modals.add.find('form')[0].reset();
        modals.add.removeClass('hidden').addClass('flex');
    });

    $(document).on('click', '.btn-edit', function () {
        const b = $(this);
        $('#edit-id').val(b.data('id'));
        $('#edit-organization').val(b.data('organization'));
        $('#edit-total_student').val(b.data('total_student'));
        $('#edit-contact').val(b.data('contact'));
        $('#edit-score').val(b.data('score'));
        $('#edit-year').val(b.data('year'));
        $('#edit-affiliation').val(b.data('affiliation'));

        if (editDropdowns) {
            editDropdowns.provinceChoices.setChoiceByValue(b.data('province') || '');
            editDropdowns.facultyChoices.setChoiceByValue(b.data('faculty') || '');
            setTimeout(() => {
                editDropdowns.majorChoices.setChoiceByValue(b.data('major') || '');
                editDropdowns.programChoices.setChoiceByValue(b.data('program') || '');
            }, 0);
            editDropdowns.mouChoices.setChoiceByValue(b.data('mou_status') || '');
            editDropdowns.affiliationChoices.setChoiceByValue(b.data('affiliation') || '');
        }
        modals.edit.removeClass('hidden').addClass('flex');
    });

    let deleteId = null;
    $(document).on('click', '.btn-delete', function () {
        deleteId = $(this).data('id');
        modals.delete.removeClass('hidden').addClass('flex');
    });

    $('[data-close-modal]').on('click', function () {
        modals[$(this).data('close-modal')].addClass('hidden').removeClass('flex');
    });

    // Forms
    const handleForm = async (e, modal) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const res = await fetch(e.target.action, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const result = await res.json().catch(() => ({ success: false, message: 'Invalid response' }));
        if (result.success) {
            dt.ajax.reload(null, false);
            modal.addClass('hidden').removeClass('flex');
        } else alert(result.message || 'Error');
    };

    $('#addForm').on('submit', e => handleForm(e, modals.add));
    $('#editForm').on('submit', e => handleForm(e, modals.edit));

    $('#confirmDeleteBtn').on('click', async () => {
        const fd = new FormData(); fd.append('id', deleteId);
        const res = await fetch('./actions/delete_internship.php', { method: 'POST', body: fd });
        const r = await res.json();
        if (r.success) {
            dt.ajax.reload(null, false);
            modals.delete.addClass('hidden').removeClass('flex');
        } else alert(r.message || 'Error');
    });
});
