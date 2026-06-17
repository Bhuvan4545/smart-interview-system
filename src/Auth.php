<?php

namespace SmartInterview;

class Auth
{
    /**
     * Check whether a session has an authenticated student.
     *
     * @param array<string,mixed> $session  Typically $_SESSION.
     */
    public static function isStudentLoggedIn(array $session): bool
    {
        return isset($session['student_id']) && $session['student_id'] > 0;
    }

    /**
     * Check whether a session has an authenticated admin.
     *
     * @param array<string,mixed> $session  Typically $_SESSION.
     */
    public static function isAdminLoggedIn(array $session): bool
    {
        return isset($session['admin_id']) && $session['admin_id'] > 0;
    }

    /**
     * Verify a plaintext password against a bcrypt hash.
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Hash a plaintext password with bcrypt.
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
