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

/**
 * Log an application error to PHP's error log.
 * Centralizes error logging so all files use a consistent format.
 */
function app_log_error(string $context, Throwable $e): void
{
    error_log(sprintf(
        '[SmartInterview] %s: %s in %s:%d',
        $context,
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    app_log_error('Database connection', $e);
    http_response_code(503);
    die('Service unavailable. Please try again later.');
}

// Start session for all pages that include this file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>