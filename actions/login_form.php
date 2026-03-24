<?php
require_once __DIR__ . '/../includes/functions.php';

$baseUrl = $_ENV['BASE_URL'] ?? '';

session_start();
date_default_timezone_set('Asia/Bangkok');

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $_SESSION['message'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    header("Location: {$baseUrl}/dashboard/login.php");
    exit;
}

try {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Initialize Session
        $_SESSION['checklogin'] = true;
        $_SESSION['email'] = $user['email'];
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Remember Me Logic (45 minutes as per original)
        $token = bin2hex(random_bytes(16));
        $expireTime = date("Y-m-d H:i:s", time() + (60 * 45));

        $stmt = $pdo->prepare("UPDATE user SET remember_token = ?, token_expire = ? WHERE id = ?");
        $stmt->execute([$token, $expireTime, $user['id']]);

        // Set Cookie
        setcookie('remember_token', $token, time() + (60 * 45), '/', '', false, true);

        header("Location: {$baseUrl}/dashboard");
        exit;
    }

    $_SESSION['message'] = $user ? 'รหัสผ่านไม่ถูกต้อง' : 'ไม่พบชื่อผู้ใช้';
    header("Location: {$baseUrl}/dashboard/login.php");
    exit;
} catch (Exception $e) {
    $_SESSION['message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    header("Location: {$baseUrl}/dashboard/login.php");
    exit;
}
