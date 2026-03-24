<?php
require_once __DIR__ . '/../includes/functions.php';

$baseUrl = $_ENV['BASE_URL'] ?? '';

session_start();

if (isset($_SESSION['id'])) {
    try {
        $pdo = db();
        $pdo->prepare("UPDATE user SET remember_token = NULL, token_expire = NULL WHERE id = ?")->execute([$_SESSION['id']]);
    } catch (Exception $e) {
        // Silent fail
    }
}

// Clear Session
session_unset();
session_destroy();

// Clear Cookies
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

header("Location: {$baseUrl}/dashboard/login.php");
exit;
