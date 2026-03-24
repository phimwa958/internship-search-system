<?php
require_once __DIR__ . '/functions.php';

$baseUrl = $_ENV['BASE_URL'] ?? '';

session_start();
date_default_timezone_set('Asia/Bangkok');

// 1. Initial Check: If no session AND no cookie, redirect to login
if (!isset($_SESSION['checklogin']) && !isset($_COOKIE['remember_token'])) {
    session_unset();
    session_destroy();
    header("Location: {$baseUrl}/dashboard/login.php");
    exit;
}

// 2. Token Logic: If there's a cookie, verify it
if (isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];

    try {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT * FROM user WHERE remember_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If token not found or expired
        if (!$user || strtotime($user['token_expire']) < time()) {
            setcookie('remember_token', '', time() - 3600, '/');

            // Clear expired token from DB
            if ($user) {
                $stmt = $pdo->prepare("UPDATE user SET remember_token = NULL, token_expire = NULL WHERE id = ?");
                $stmt->execute([$user['id']]);
            }

            session_unset();
            session_destroy();
            header("Location: {$baseUrl}/dashboard/login.php");
            exit;
        }

        // If session is lost but token is still valid, restore session
        if (!isset($_SESSION['checklogin'])) {
            $_SESSION['checklogin'] = true;
            $_SESSION['email'] = $user['email'];
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
        }
    } catch (Exception $e) {
        // Log error if needed, for now just redirect
        header("Location: {$baseUrl}/dashboard/login.php");
        exit;
    }
}
