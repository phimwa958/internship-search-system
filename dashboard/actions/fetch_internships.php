<?php
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$request = $_POST ?: $_GET;

// Argument that sent form the datatables
$draw = isset($request['draw']) ? (int) $request['draw'] : 0;
$start = isset($request['start']) ? (int) $request['start'] : 0;
$length = isset($request['length']) ? (int) $request['length'] : 10;

// Search query from user
$searchValue = $request['search']['value'] ?? '';

// Map index to real column (field) in the database
$columnsMap = [
    0 => 'ist.id',
    1 => 'ist.organization',
    2 => 'ist.province',
    3 => 'fpm.faculty',
    4 => 'fpm.program',
    5 => 'fpm.major',
    6 => 'ist.year',
    7 => 'ist.total_student',
    8 => 'ist.mou_status',
    9 => 'ist.contact',
    10 => 'ist.score',
    11 => 'ist.affiliation',
    12 => 'ist.created_at',
];

$orderColumnIndex = isset($request['order'][0]['column']) ? (int) $request['order'][0]['column'] : 0;
$orderDir = (isset($request['order'][0]['dir']) && strtolower($request['order'][0]['dir']) === 'desc') ? 'DESC' : 'ASC';
$orderColumn = $columnsMap[$orderColumnIndex] ?? 'ist.id';

$baseFrom = "
    FROM internship_stats ist
    INNER JOIN faculty_program_major fpm
        ON ist.major_id = fpm.id
";

try {
    $pdo = db();

    // Total records
    $recordsTotal = (int) db_count("SELECT COUNT(*) " . $baseFrom);

    // Filter logic
    $where = '';
    $params = [];
    if ($searchValue !== '') {
        $where = "
            WHERE
                ist.organization LIKE ?
                OR ist.province LIKE ?
                OR fpm.faculty LIKE ?
                OR fpm.program LIKE ?
                OR fpm.major LIKE ?
                OR ist.contact LIKE ?
                OR CAST(ist.total_student AS CHAR) LIKE ?
                OR ist.year LIKE ?
                OR CAST(ist.score AS CHAR) LIKE ?
                OR CAST(ist.mou_status AS CHAR) LIKE ?
                OR CAST(ist.affiliation AS CHAR) LIKE ?
        ";
        $searchParam = '%' . $searchValue . '%';
        $params = array_fill(0, 11, $searchParam);
    }

    // Filtered records
    $recordsFiltered = (int) db_count("SELECT COUNT(*) " . $baseFrom . ' ' . $where, $params);

    // Fetch data
    $sqlData = "
        SELECT ist.*, fpm.faculty, fpm.program, fpm.major
        " . $baseFrom . ' ' . $where . "
        ORDER BY $orderColumn $orderDir
        LIMIT ?, ?
    ";

    $stmtData = $pdo->prepare($sqlData);
    $i = 1;
    foreach ($params as $val) {
        $stmtData->bindValue($i++, $val);
    }
    $stmtData->bindValue($i++, (int) $start, PDO::PARAM_INT);
    $stmtData->bindValue($i++, (int) $length, PDO::PARAM_INT);
    $stmtData->execute();

    $data = $stmtData->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
