<?php
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$email = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$role = trim($_POST['role'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($id <= 0 || !$email || !$username || !$role) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

try {
    $pdo = db();
    $user = db_fetch_all("SELECT id FROM user WHERE id = ?", [$id]);
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลผู้ใช้']);
        exit;
    }

    if ($password !== '') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE user SET email = ?, username = ?, role = ?, password = ? WHERE id = ?";
        $params = [$email, $username, $role, $hashedPassword, $id];
    } else {
        $sql = "UPDATE user SET email = ?, username = ?, role = ? WHERE id = ?";
        $params = [$email, $username, $role, $id];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => 'อัปเดตผู้ใช้สำเร็จ']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
