<?php
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $faculty = $_POST['faculty'] ?? null;
    $program = $_POST['program'] ?? null;
    $major = $_POST['major'] ?? null;
    $province = $_POST['province'] ?? null;
    $academicYear = $_POST['academic-year'] ?? null;

    $draw = (int) ($_POST['draw'] ?? 0);
    $start = max(0, (int) ($_POST['start'] ?? 0));
    $length = max(1, (int) ($_POST['length'] ?? 10));
    $query = $_POST['search']['value'] ?? '';

    $cols = [
        1 => 'internship_stats.organization',
        2 => 'internship_stats.province',
        3 => 'faculty_program_major.faculty',
        4 => 'faculty_program_major.program',
        5 => 'faculty_program_major.major',
        6 => 'internship_stats.year',
        7 => 'internship_stats.affiliation',
        8 => 'internship_stats.total_student',
        9 => 'internship_stats.mou_status',
        10 => 'internship_stats.contact',
        11 => 'internship_stats.score',
    ];

    $orderParts = [];
    if (!empty($_POST['order'])) {
        foreach ($_POST['order'] as $order) {
            $index = (int) ($order['column'] ?? -1);
            $direction = (strtolower($order['dir'] ?? 'asc') === 'desc') ? 'DESC' : 'ASC';
            if (isset($cols[$index])) {
                $orderParts[] = $cols[$index] . ' ' . $direction;
            }
        }
    }
    $orderSql = 'ORDER BY ' . ($orderParts ? implode(', ', $orderParts) : 'internship_stats.year DESC, internship_stats.organization ASC');

    $whereParts = [];
    $params = [];

    if ($query !== '') {
        $whereParts[] = '(
            internship_stats.organization LIKE :query OR
            internship_stats.province LIKE :query OR
            faculty_program_major.faculty LIKE :query OR
            faculty_program_major.program LIKE :query OR
            faculty_program_major.major LIKE :query OR
            internship_stats.contact LIKE :query OR
            CAST(internship_stats.total_student AS CHAR) LIKE :query OR
            internship_stats.mou_status LIKE :query OR
            CAST(internship_stats.year AS CHAR) LIKE :query OR
            CAST(internship_stats.score AS CHAR) LIKE :query OR
            CAST(internship_stats.affiliation AS CHAR) LIKE :query 
        )';
        $params[':query'] = '%' . $query . '%';
    }

    if (!empty($faculty)) {
        $whereParts[] = 'faculty_program_major.faculty = :faculty';
        $params[':faculty'] = $faculty;
    }
    if (!empty($program)) {
        $whereParts[] = 'faculty_program_major.program = :program';
        $params[':program'] = $program;
    }
    if (!empty($major)) {
        $whereParts[] = 'faculty_program_major.major = :major';
        $params[':major'] = $major;
    }
    if (!empty($province)) {
        $whereParts[] = 'internship_stats.province = :province';
        $params[':province'] = $province;
    }
    if (!empty($academicYear)) {
        $whereParts[] = 'internship_stats.year = :year';
        $params[':year'] = (int) $academicYear;
    }

    $whereSql = $whereParts ? 'WHERE ' . implode(' AND ', $whereParts) : '';

    $countBase = "FROM internship_stats INNER JOIN faculty_program_major ON internship_stats.major_id = faculty_program_major.id";
    $total = db_count("SELECT COUNT(*) $countBase");
    $filtered = db_count("SELECT COUNT(*) $countBase $whereSql", $params);

    $sql = "
        SELECT faculty_program_major.faculty, faculty_program_major.program, faculty_program_major.major,
               internship_stats.organization, internship_stats.province, internship_stats.year,
               internship_stats.total_student, internship_stats.mou_status, internship_stats.contact,
               internship_stats.score, internship_stats.affiliation
        FROM internship_stats
        INNER JOIN faculty_program_major ON internship_stats.major_id = faculty_program_major.id
        $whereSql $orderSql LIMIT :start, :len
    ";

    $stmt = db()->prepare($sql);
    foreach ($params as $key => $value)
        $stmt->bindValue($key, $value);
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':len', $length, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => $total,
        'recordsFiltered' => $filtered,
        'data' => array_map(fn($row) => [
            'organization' => $row['organization'] ?? '',
            'province' => $row['province'] ?? '',
            'faculty' => $row['faculty'] ?? '',
            'program' => $row['program'] ?? '',
            'major' => $row['major'] ?? '',
            'year' => (string) ($row['year'] ?? ''),
            'total_student' => (int) ($row['total_student'] ?? 0),
            'mou_status' => $row['mou_status'] ?? '',
            'contact' => $row['contact'] ?? '',
            'score' => (string) ($row['score'] ?? ''),
            'affiliation' => $row['affiliation'] ?? '',
        ], $rows),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server Error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
