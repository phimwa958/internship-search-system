document.addEventListener("DOMContentLoaded", function () {
    // These values should be passed from the PHP script by setting data attributes on the form or global variables
    const filterForm = document.getElementById("filter-form");
    if (!filterForm) return;

    const facultyMajorsPrograms = JSON.parse(filterForm.dataset.facultyMap || "{}");
    const selectedFaculty = filterForm.dataset.selectedFaculty || "";
    const selectedMajor = filterForm.dataset.selectedMajor || "";
    const selectedProgram = filterForm.dataset.selectedProgram || "";
    const selectedProvince = filterForm.dataset.selectedProvince || "";
    const selectedAcademicYear = filterForm.dataset.selectedAcademicYear || "";
    const yearsArray = JSON.parse(filterForm.dataset.yearsArray || "[]");

    const sortChoice = (a, b) => {
        if (a.value === '' && b.value !== '') return -1;
        if (a.value !== '' && b.value === '') return 1;
        return a.label.localeCompare(b.label, 'th');
    }

    const sortChoiceDesc = (a, b) => {
        if (a.value === '' && b.value !== '') return 1;
        if (a.value !== '' && b.value === '') return -1;
        return b.label.localeCompare(a.label, 'th');
    }

    const facultySelect = document.getElementById("faculty");
    const majorSelect = document.getElementById("major");
    const programSelect = document.getElementById("program");
    const provinceSelect = document.getElementById("province");
    const academicYearSelect = document.getElementById("academic-year");

    const facultyChoices = new Choices(facultySelect, {
        searchEnabled: true,
        itemSelectText: "",
        searchPlaceholderValue: "พิมพ์เพื่อค้นหาคณะ...",
        sorter: sortChoice,
    });
    const majorChoices = new Choices(majorSelect, {
        searchEnabled: true,
        itemSelectText: "",
        searchPlaceholderValue: "พิมพ์เพื่อค้นหาสาขา...",
        sorter: sortChoice,
    });
    const programChoices = new Choices(programSelect, {
        searchEnabled: true,
        itemSelectText: "",
        searchPlaceholderValue: "พิมพ์เพื่อค้นหาหลักสูตร...",
        sorter: sortChoice,
    });
    const provinceChoices = new Choices(provinceSelect, {
        searchEnabled: true,
        itemSelectText: "",
        searchPlaceholderValue: "พิมพ์เพื่อค้นหาจังหวัด...",
        sorter: sortChoice,
    });
    const academicYearChoices = new Choices(academicYearSelect, {
        searchEnabled: true,
        itemSelectText: "",
        searchPlaceholderValue: "พิมพ์เพื่อค้นหาปีการศึกษา...",
        sorter: sortChoiceDesc,
    });

    const allFaculties = Object.keys(facultyMajorsPrograms);
    const allMajors = Object.values(facultyMajorsPrograms).flatMap(majorsObj => Object.keys(majorsObj));
    const allPrograms = [...new Set(Object.values(facultyMajorsPrograms).flatMap(majorsObj => Object.values(majorsObj)))];

    const populateFaculties = (list) => {
        facultyChoices.clearStore();
        facultyChoices.setChoices(
            [{ value: "", label: "-เลือกคณะ-", selected: true }].concat(list.map(f => ({ value: f, label: f }))),
            "value", "label", true
        );
    };
    const populateMajors = (list) => {
        majorChoices.clearStore();
        majorChoices.setChoices(
            [{ value: "", label: "-เลือกสาขา-", selected: true }].concat(list.map(m => ({ value: m, label: m }))),
            "value", "label", true
        );
    };
    const populatePrograms = (list) => {
        programChoices.clearStore();
        programChoices.setChoices(
            [{ value: "", label: "-เลือกหลักสูตร-", selected: true }].concat(list.map(p => ({ value: p, label: p }))),
            "value", "label", true
        );
    };
    const populateProvinces = (list) => {
        provinceChoices.clearStore();
        provinceChoices.setChoices(
            [{ value: "", label: "-เลือกจังหวัด-", selected: true }].concat(list.map(p => ({ value: p, label: p }))),
            "value", "label", true
        );
    };
    const populateAcademicYears = (list) => {
        academicYearChoices.clearStore();
        academicYearChoices.setChoices(
            [{ value: "", label: "-เลือกปีการศึกษา-", selected: true }].concat(list.map(y => ({ value: y, label: y }))),
            "value", "label", true
        );
    };

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

    populateFaculties(allFaculties);
    populateMajors(allMajors);
    populatePrograms(allPrograms);
    populateProvinces(provinces);
    populateAcademicYears(yearsArray.map(String));

    if (selectedFaculty) facultyChoices.setChoiceByValue(selectedFaculty);
    if (selectedMajor) majorChoices.setChoiceByValue(selectedMajor);
    if (selectedProgram) programChoices.setChoiceByValue(selectedProgram);
    if (selectedProvince) provinceChoices.setChoiceByValue(selectedProvince);
    if (selectedAcademicYear) academicYearChoices.setChoiceByValue(selectedAcademicYear);

    facultySelect.addEventListener("change", () => {
        const faculty = facultySelect.value;
        const currentProgram = programSelect.value || "";

        const getProgramsOfFaculty = (name) => (name && facultyMajorsPrograms[name]) ? [...new Set(Object.values(facultyMajorsPrograms[name]))] : allPrograms;
        const getMajorsOfFaculty = (name) => (name && facultyMajorsPrograms[name]) ? Object.keys(facultyMajorsPrograms[name]) : allMajors;

        if (faculty) {
            const programsOfFaculty = getProgramsOfFaculty(faculty);
            const programToKeep = currentProgram && programsOfFaculty.includes(currentProgram) ? currentProgram : "";
            let majorsList = programToKeep ? Object.entries(facultyMajorsPrograms[faculty]).filter(([, p]) => p === programToKeep).map(([m]) => m) : getMajorsOfFaculty(faculty);

            populateMajors(majorsList);
            populatePrograms(programsOfFaculty);
            programChoices.setChoiceByValue(programToKeep || "");
            return;
        }

        populateMajors(allMajors);
        populatePrograms(allPrograms);
        programChoices.setChoiceByValue("");
        facultyChoices.setChoiceByValue("");
    });

    majorSelect.addEventListener("change", () => {
        const major = majorSelect.value;
        if (!major) return;

        for (const [fac, majorsObj] of Object.entries(facultyMajorsPrograms)) {
            if (major in majorsObj) {
                facultyChoices.setChoiceByValue(fac);
                const programsOfFaculty = [...new Set(Object.values(facultyMajorsPrograms[fac]))];
                populatePrograms(programsOfFaculty);
                programChoices.setChoiceByValue(majorsObj[major] || "");
                break;
            }
        }
    });

    programSelect.addEventListener("change", () => {
        const prog = programSelect.value;
        if (!prog) {
            populateFaculties(allFaculties);
            populateMajors(allMajors);
            populatePrograms(allPrograms);
            facultyChoices.setChoiceByValue("");
            majorChoices.setChoiceByValue("");
            return;
        }

        const majorsOfProgram = [];
        const facultiesOfProgramSet = new Set();
        for (const [fac, majorsObj] of Object.entries(facultyMajorsPrograms)) {
            for (const [major, pName] of Object.entries(majorsObj)) {
                if (pName === prog) {
                    majorsOfProgram.push(major);
                    facultiesOfProgramSet.add(fac);
                }
            }
        }

        const currentFaculty = facultySelect.value || "";
        if (currentFaculty && facultiesOfProgramSet.has(currentFaculty)) {
            const majorsInSelectedFaculty = Object.entries(facultyMajorsPrograms[currentFaculty]).filter(([, pName]) => pName === prog).map(([m]) => m);
            populateFaculties([currentFaculty]);
            facultyChoices.setChoiceByValue(currentFaculty);
            populateMajors(majorsInSelectedFaculty);
            majorChoices.setChoiceByValue("");
            populatePrograms([...new Set(Object.values(facultyMajorsPrograms[currentFaculty]))]);
            programChoices.setChoiceByValue(prog);
        } else {
            populateFaculties([...facultiesOfProgramSet]);
            populateMajors(majorsOfProgram);
            facultyChoices.setChoiceByValue("");
            majorChoices.setChoiceByValue("");
            populatePrograms([prog]);
            programChoices.setChoiceByValue(prog);
        }
    });

    const clearSearchButton = document.getElementById('clear-search-query');
    if (clearSearchButton) {
        clearSearchButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = window.location.pathname;
        });
    }
});
