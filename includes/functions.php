<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Initialize Environment Variables
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Global Configuration
date_default_timezone_set('Asia/Bangkok');

/**
 * Returns a PDO database connection instance.
 * Uses a global/static variable for simple singleton-like access.
 */
function db()
{
    static $conn = null;
    if ($conn === null) {
        $dbServerName = $_ENV['DB_SERVER_NAME'];
        $dbName = $_ENV['DB_NAME'];
        $dbUsername = $_ENV['DB_USERNAME'];
        $dbPassword = $_ENV['DB_PASSWORD'];

        try {
            $dsn = "mysql:host=$dbServerName;dbname=$dbName;charset=utf8mb4";
            $conn = new PDO($dsn, $dbUsername, $dbPassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        } catch (PDOException $e) {
            die('❌ Failed to connect: ' . $e->getMessage());
        }
    }
    return $conn;
}

/**
 * Executes a SELECT query and returns all result rows.
 */
function db_fetch_all($sql, $params = [])
{
    $stmt = db()->prepare($sql);
    $i = 1;
    foreach ($params as $value) {
        if (is_int($value)) {
            $stmt->bindValue($i++, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($i++, $value);
        }
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Executes a query and returns a single column.
 */
function db_fetch_column($sql, $params = [])
{
    $stmt = db()->prepare($sql);
    $i = 1;
    foreach ($params as $value) {
        if (is_int($value)) {
            $stmt->bindValue($i++, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($i++, $value);
        }
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Executes a SELECT query and returns the first result row.
 */
function db_fetch_one($sql, $params = [])
{
    $stmt = db()->prepare($sql);
    $i = 1;
    foreach ($params as $value) {
        if (is_int($value)) {
            $stmt->bindValue($i++, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($i++, $value);
        }
    }
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Returns the count of records based on a query.
 */
function db_count($sql, $params = [])
{
    $stmt = db()->prepare($sql);
    $i = 1;
    foreach ($params as $value) {
        if (is_int($value)) {
            $stmt->bindValue($i++, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($i++, $value);
        }
    }
    $stmt->execute();
    return (int) $stmt->fetchColumn();
}
