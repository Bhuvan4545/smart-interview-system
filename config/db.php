<?php
// Load environment variables from .env when the file exists (production
// servers should set real env vars instead).
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        putenv(trim($line));
    }
}

// Database configuration — credentials come from environment variables.
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'smart_interview_system';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    die('Database connection failed. Please try again later.');
}

// ---------------------------------------------------------------------------
// Secure session configuration
// ---------------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isSecure,
        'httponly'  => true,
        'samesite' => 'Strict',
    ]);

    session_start();
}

// ---------------------------------------------------------------------------
// Security response headers
// ---------------------------------------------------------------------------
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

// ---------------------------------------------------------------------------
// CSRF-token helpers (included on every page via this file)
// ---------------------------------------------------------------------------

/**
 * Return the current CSRF token, generating one if it does not exist.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output a hidden <input> element containing the CSRF token.
 */
function csrf_field(): void
{
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/**
 * Validate the submitted CSRF token against the session token.
 * Call this at the top of every POST handler.
 */
function csrf_validate(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return $token !== '' && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}
?>
