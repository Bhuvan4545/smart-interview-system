<?php
// Database configuration and connection using PDO
$host = 'localhost';
$db   = 'smart_interview_system';
$user = 'root';          // XAMPP default
$pass = '';              // XAMPP default (empty)

// Data Source Name
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}

// Start session for all pages that include this file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>