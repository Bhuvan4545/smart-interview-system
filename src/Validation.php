<?php

namespace SmartInterview;

class Validation
{
    /**
     * Validate student registration input.
     *
     * @return string[] List of error messages (empty if valid).
     */
    public static function validateRegistration(
        string $name,
        string $email,
        string $password,
        string $confirmPassword
    ): array {
        $errors = [];

        if (trim($name) === '') {
            $errors[] = 'Name is required.';
        }

        if (trim($email) === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email is required.';
        }

        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        return $errors;
    }

    /**
     * Validate login input.
     *
     * @return string[] List of error messages (empty if valid).
     */
    public static function validateLogin(string $email, string $password): array
    {
        $errors = [];

        if (trim($email) === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email.';
        }

        if ($password === '') {
            $errors[] = 'Password is required.';
        }

        return $errors;
    }

    /**
     * Validate change-password input.
     *
     * @return string[] List of error messages (empty if valid).
     */
    public static function validateChangePassword(
        string $currentPassword,
        string $newPassword,
        string $confirmPassword
    ): array {
        $errors = [];

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $errors[] = 'All fields are required.';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'New password must be at least 6 characters.';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'New password and confirm password do not match.';
        }

        return $errors;
    }

    /**
     * Validate question input (add / edit).
     *
     * @return string[] List of error messages (empty if valid).
     */
    public static function validateQuestion(
        string $question,
        string $category,
        string $difficulty,
        string $option1,
        string $option2,
        string $option3,
        string $option4,
        int $correctOption
    ): array {
        $errors = [];

        if (trim($question) === '') {
            $errors[] = 'Question text is required.';
        }
        if (trim($category) === '') {
            $errors[] = 'Category is required.';
        }
        if (!in_array($difficulty, ['Easy', 'Medium', 'Hard'], true)) {
            $errors[] = 'Select a valid difficulty.';
        }
        if (trim($option1) === '' || trim($option2) === '' || trim($option3) === '' || trim($option4) === '') {
            $errors[] = 'All options are required.';
        }
        if ($correctOption < 1 || $correctOption > 4) {
            $errors[] = 'Select a valid correct option.';
        }

        return $errors;
    }
}
