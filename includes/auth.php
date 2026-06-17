<?php
/**
 * Authentication guard utilities.
 *
 * Usage:
 *   require_once __DIR__ . '/../includes/auth.php';
 *   require_admin();   // redirects to admin login if not authenticated
 *   require_student(); // redirects to student login if not authenticated
 */

/**
 * Ensure the current session belongs to an authenticated admin.
 * Redirects to admin/login.php if the session is invalid.
 */
function require_admin(): void
{
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Ensure the current session belongs to an authenticated student.
 * Redirects to student/login.php if the session is invalid.
 */
function require_student(): void
{
    if (!isset($_SESSION['student_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Destroy the session and redirect to login.
 */
function logout_and_redirect(): void
{
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}
