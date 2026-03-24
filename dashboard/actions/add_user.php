<?php
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = trim($_POST['role'] ?? 'user');

if (!$email || !$username || !$password) {
    echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'รูปแบบอีเมลไม่ถูกต้อง']);
    exit;
}

$allowedRoles = ['user', 'admin'];
if (!in_array($role, $allowedRoles, true)) {
    $role = 'user';
}

try {
    $pdo = db();
    $exists = db_fetch_all("SELECT id FROM user WHERE email = ?", [$email]);
    if ($exists) {
        echo json_encode(['success' => false, 'message' => 'อีเมลนี้มีผู้ใช้งานแล้ว']);
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmtInsert = $pdo->prepare("INSERT INTO user (email, username, password, role) VALUES (?, ?, ?, ?)");
    $stmtInsert->execute([$email, $username, $passwordHash, $role]);

    echo json_encode(['success' => true, 'message' => 'เพิ่มผู้ใช้สำเร็จ']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
