<?php

namespace SmartInterview\Tests;

use PHPUnit\Framework\TestCase;
use SmartInterview\Validation;

class ValidationTest extends TestCase
{
    // ---------------------------------------------------------------
    // Registration validation
    // ---------------------------------------------------------------

    public function testRegistrationValidInputReturnsNoErrors(): void
    {
        $errors = Validation::validateRegistration('John Doe', 'john@example.com', 'secret123', 'secret123');
        $this->assertEmpty($errors);
    }

    public function testRegistrationEmptyNameReturnsError(): void
    {
        $errors = Validation::validateRegistration('', 'john@example.com', 'secret123', 'secret123');
        $this->assertContains('Name is required.', $errors);
    }

    public function testRegistrationWhitespaceOnlyNameReturnsError(): void
    {
        $errors = Validation::validateRegistration('   ', 'john@example.com', 'secret123', 'secret123');
        $this->assertContains('Name is required.', $errors);
    }

    public function testRegistrationEmptyEmailReturnsError(): void
    {
        $errors = Validation::validateRegistration('John', '', 'secret123', 'secret123');
        $this->assertContains('A valid email is required.', $errors);
    }

    public function testRegistrationInvalidEmailReturnsError(): void
    {
        $errors = Validation::validateRegistration('John', 'not-an-email', 'secret123', 'secret123');
        $this->assertContains('A valid email is required.', $errors);
    }

    public function testRegistrationShortPasswordReturnsError(): void
    {
        $errors = Validation::validateRegistration('John', 'john@example.com', 'abc', 'abc');
        $this->assertContains('Password must be at least 6 characters.', $errors);
    }

    public function testRegistrationPasswordMismatchReturnsError(): void
    {
        $errors = Validation::validateRegistration('John', 'john@example.com', 'secret123', 'different');
        $this->assertContains('Passwords do not match.', $errors);
    }

    public function testRegistrationMultipleErrorsReturnedAtOnce(): void
    {
        $errors = Validation::validateRegistration('', '', 'abc', 'xyz');
        $this->assertContains('Name is required.', $errors);
        $this->assertContains('A valid email is required.', $errors);
        $this->assertContains('Password must be at least 6 characters.', $errors);
        $this->assertContains('Passwords do not match.', $errors);
    }

    public function testRegistrationExactlySixCharPasswordIsValid(): void
    {
        $errors = Validation::validateRegistration('John', 'j@e.co', '123456', '123456');
        $this->assertEmpty($errors);
    }

    // ---------------------------------------------------------------
    // Login validation
    // ---------------------------------------------------------------

    public function testLoginValidInputReturnsNoErrors(): void
    {
        $errors = Validation::validateLogin('john@example.com', 'secret');
        $this->assertEmpty($errors);
    }

    public function testLoginEmptyEmailReturnsError(): void
    {
        $errors = Validation::validateLogin('', 'secret');
        $this->assertContains('Enter a valid email.', $errors);
    }

    public function testLoginInvalidEmailReturnsError(): void
    {
        $errors = Validation::validateLogin('bad-email', 'secret');
        $this->assertContains('Enter a valid email.', $errors);
    }

    public function testLoginEmptyPasswordReturnsError(): void
    {
        $errors = Validation::validateLogin('john@example.com', '');
        $this->assertContains('Password is required.', $errors);
    }

    public function testLoginBothFieldsEmptyReturnsTwoErrors(): void
    {
        $errors = Validation::validateLogin('', '');
        $this->assertCount(2, $errors);
    }

    // ---------------------------------------------------------------
    // Change password validation
    // ---------------------------------------------------------------

    public function testChangePasswordValidInputReturnsNoErrors(): void
    {
        $errors = Validation::validateChangePassword('old123', 'newpass', 'newpass');
        $this->assertEmpty($errors);
    }

    public function testChangePasswordEmptyFieldsReturnsError(): void
    {
        $errors = Validation::validateChangePassword('', 'newpass', 'newpass');
        $this->assertContains('All fields are required.', $errors);
    }

    public function testChangePasswordNewPasswordTooShortReturnsError(): void
    {
        $errors = Validation::validateChangePassword('old123', 'abc', 'abc');
        $this->assertContains('New password must be at least 6 characters.', $errors);
    }

    public function testChangePasswordMismatchReturnsError(): void
    {
        $errors = Validation::validateChangePassword('old123', 'newpass1', 'newpass2');
        $this->assertContains('New password and confirm password do not match.', $errors);
    }

    public function testChangePasswordEmptyCurrentOnly(): void
    {
        $errors = Validation::validateChangePassword('', 'newpass', 'newpass');
        $this->assertContains('All fields are required.', $errors);
        $this->assertCount(1, $errors);
    }

    public function testChangePasswordEmptyNewOnly(): void
    {
        $errors = Validation::validateChangePassword('old123', '', '');
        $this->assertContains('All fields are required.', $errors);
    }

    public function testChangePasswordExactlySixCharsIsValid(): void
    {
        $errors = Validation::validateChangePassword('old123', '123456', '123456');
        $this->assertEmpty($errors);
    }

    // ---------------------------------------------------------------
    // Question validation
    // ---------------------------------------------------------------

    public function testQuestionValidInputReturnsNoErrors(): void
    {
        $errors = Validation::validateQuestion(
            'What is OOP?',
            'Technical',
            'Medium',
            'A', 'B', 'C', 'D',
            2
        );
        $this->assertEmpty($errors);
    }

    public function testQuestionEmptyTextReturnsError(): void
    {
        $errors = Validation::validateQuestion('', 'Tech', 'Easy', 'A', 'B', 'C', 'D', 1);
        $this->assertContains('Question text is required.', $errors);
    }

    public function testQuestionEmptyCategoryReturnsError(): void
    {
        $errors = Validation::validateQuestion('Q?', '', 'Easy', 'A', 'B', 'C', 'D', 1);
        $this->assertContains('Category is required.', $errors);
    }

    public function testQuestionInvalidDifficultyReturnsError(): void
    {
        $errors = Validation::validateQuestion('Q?', 'Tech', 'Expert', 'A', 'B', 'C', 'D', 1);
        $this->assertContains('Select a valid difficulty.', $errors);
    }

    public function testQuestionEmptyOptionReturnsError(): void
    {
        $errors = Validation::validateQuestion('Q?', 'Tech', 'Easy', '', 'B', 'C', 'D', 1);
        $this->assertContains('All options are required.', $errors);
    }

    public function testQuestionCorrectOptionZeroReturnsError(): void
    {
        $errors = Validation::validateQuestion('Q?', 'Tech', 'Easy', 'A', 'B', 'C', 'D', 0);
        $this->assertContains('Select a valid correct option.', $errors);
    }

    public function testQuestionCorrectOptionFiveReturnsError(): void
    {
        $errors = Validation::validateQuestion('Q?', 'Tech', 'Easy', 'A', 'B', 'C', 'D', 5);
        $this->assertContains('Select a valid correct option.', $errors);
    }

    public function testQuestionAllDifficultyLevelsAccepted(): void
    {
        foreach (['Easy', 'Medium', 'Hard'] as $level) {
            $errors = Validation::validateQuestion('Q?', 'Cat', $level, 'A', 'B', 'C', 'D', 1);
            $this->assertEmpty($errors, "Difficulty '$level' should be valid");
        }
    }

    public function testQuestionCorrectOptionBoundaries(): void
    {
        $errors1 = Validation::validateQuestion('Q?', 'Cat', 'Easy', 'A', 'B', 'C', 'D', 1);
        $this->assertEmpty($errors1);

        $errors4 = Validation::validateQuestion('Q?', 'Cat', 'Easy', 'A', 'B', 'C', 'D', 4);
        $this->assertEmpty($errors4);
    }

    public function testQuestionMultipleErrors(): void
    {
        $errors = Validation::validateQuestion('', '', 'Invalid', '', '', '', '', 0);
        $this->assertGreaterThanOrEqual(5, count($errors));
    }
}
