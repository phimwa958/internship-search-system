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
    1 => 'email',
    2 => 'username',
    3 => 'role',
];

$orderColumnIndex = (int) ($request['order'][0]['column'] ?? 0);
$orderDir = (isset($request['order'][0]['dir']) && strtolower($request['order'][0]['dir']) === 'desc') ? 'DESC' : 'ASC';
$orderColumn = $columnsMap[$orderColumnIndex] ?? 'id';

try {
    $pdo = db();
    $recordsTotal = db_count("SELECT COUNT(*) FROM user");

    $where = '';
    $params = [];
    if (!empty($searchValue)) {
        $where = " WHERE email LIKE ? OR username LIKE ? OR role LIKE ? ";
        $params = ["%$searchValue%", "%$searchValue%", "%$searchValue%"];
    }

    $recordsFiltered = db_count("SELECT COUNT(*) FROM user $where", $params);

    $sqlData = "SELECT id, email, username, role FROM user $where ORDER BY $orderColumn $orderDir LIMIT ?, ?";
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
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
