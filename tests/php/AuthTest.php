<?php

namespace SmartInterview\Tests;

use PHPUnit\Framework\TestCase;
use SmartInterview\Auth;

class AuthTest extends TestCase
{
    // ---------------------------------------------------------------
    // Student session checks
    // ---------------------------------------------------------------

    public function testStudentLoggedInWithValidSession(): void
    {
        $session = ['student_id' => 1, 'student_name' => 'Alice'];
        $this->assertTrue(Auth::isStudentLoggedIn($session));
    }

    public function testStudentNotLoggedInWhenKeyMissing(): void
    {
        $session = ['some_other_key' => 'value'];
        $this->assertFalse(Auth::isStudentLoggedIn($session));
    }

    public function testStudentNotLoggedInWhenIdIsZero(): void
    {
        $session = ['student_id' => 0];
        $this->assertFalse(Auth::isStudentLoggedIn($session));
    }

    public function testStudentNotLoggedInWhenIdIsNegative(): void
    {
        $session = ['student_id' => -1];
        $this->assertFalse(Auth::isStudentLoggedIn($session));
    }

    public function testStudentNotLoggedInWithEmptySession(): void
    {
        $this->assertFalse(Auth::isStudentLoggedIn([]));
    }

    // ---------------------------------------------------------------
    // Admin session checks
    // ---------------------------------------------------------------

    public function testAdminLoggedInWithValidSession(): void
    {
        $session = ['admin_id' => 1, 'admin_email' => 'admin@admin.com'];
        $this->assertTrue(Auth::isAdminLoggedIn($session));
    }

    public function testAdminNotLoggedInWhenKeyMissing(): void
    {
        $session = [];
        $this->assertFalse(Auth::isAdminLoggedIn($session));
    }

    public function testAdminNotLoggedInWhenIdIsZero(): void
    {
        $session = ['admin_id' => 0];
        $this->assertFalse(Auth::isAdminLoggedIn($session));
    }

    // ---------------------------------------------------------------
    // Password hashing & verification
    // ---------------------------------------------------------------

    public function testHashAndVerifyPassword(): void
    {
        $password = 'mySecretP@ss';
        $hash = Auth::hashPassword($password);

        $this->assertNotEquals($password, $hash);
        $this->assertTrue(Auth::verifyPassword($password, $hash));
    }

    public function testVerifyPasswordFailsWithWrongPassword(): void
    {
        $hash = Auth::hashPassword('correct');
        $this->assertFalse(Auth::verifyPassword('wrong', $hash));
    }

    public function testHashProducesDifferentHashesForSamePassword(): void
    {
        $hash1 = Auth::hashPassword('samepass');
        $hash2 = Auth::hashPassword('samepass');
        $this->assertNotEquals($hash1, $hash2);
    }

    public function testHashedPasswordHasBcryptPrefix(): void
    {
        $hash = Auth::hashPassword('test123');
        $this->assertStringStartsWith('$2y$', $hash);
    }

    public function testEmptyPasswordCanBeHashed(): void
    {
        $hash = Auth::hashPassword('');
        $this->assertTrue(Auth::verifyPassword('', $hash));
        $this->assertFalse(Auth::verifyPassword('notempty', $hash));
    }

    public function testVerifyPasswordWithSpecialCharacters(): void
    {
        $password = '!@#$%^&*()_+-=[]{}|;:,.<>?/~`';
        $hash = Auth::hashPassword($password);
        $this->assertTrue(Auth::verifyPassword($password, $hash));
    }

    public function testVerifyPasswordWithUnicode(): void
    {
        $password = 'p@sswörd_日本語';
        $hash = Auth::hashPassword($password);
        $this->assertTrue(Auth::verifyPassword($password, $hash));
    }
}
