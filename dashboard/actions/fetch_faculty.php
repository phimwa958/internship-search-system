<?php
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$request = $_POST ?: $_GET;

$draw = (int) ($request['draw'] ?? 0);
$start = (int) ($request['start'] ?? 0);
$length = (int) ($request['length'] ?? 10);
$searchValue = $request['search']['value'] ?? '';

$columnsMap = [
    0 => 'id',
    1 => 'faculty',
    2 => 'program',
    3 => 'major',
];

$orderColumnIndex = (int) ($request['order'][0]['column'] ?? 0);
$orderDir = (isset($request['order'][0]['dir']) && strtolower($request['order'][0]['dir']) === 'desc') ? 'DESC' : 'ASC';
$orderColumn = $columnsMap[$orderColumnIndex] ?? 'id';

try {
    $pdo = db();

    // Total records
    $recordsTotal = db_count("SELECT COUNT(*) FROM faculty_program_major");

    $where = '';
    $params = [];
    if (!empty($searchValue)) {
        $where = " WHERE faculty LIKE ? OR program LIKE ? OR major LIKE ? ";
        $params = ["%$searchValue%", "%$searchValue%", "%$searchValue%"];
    }

    $recordsFiltered = db_count("SELECT COUNT(*) FROM faculty_program_major $where", $params);

    // Data
    $sqlData = "SELECT id, faculty, program, major FROM faculty_program_major $where ORDER BY $orderColumn $orderDir LIMIT ?, ?";
    $stmt = $pdo->prepare($sqlData);
    $i = 1;
    foreach ($params as $val) {
        $stmt->bindValue($i++, $val);
    }
    $stmt->bindValue($i++, (int) $start, PDO::PARAM_INT);
    $stmt->bindValue($i++, (int) $length, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
