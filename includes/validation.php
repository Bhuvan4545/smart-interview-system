<?php
/**
 * Shared validation utilities.
 */

/**
 * Validate login form fields (email + password).
 *
 * @param string $email
 * @param string $password
 * @return string[] Array of error messages (empty if valid).
 */
function validate_login(string $email, string $password): array
{
    $errors = [];

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    return $errors;
}

/**
 * Validate change-password form fields.
 *
 * @param string $currentPassword
 * @param string $newPassword
 * @param string $confirmPassword
 * @return string[] Array of error messages (empty if valid).
 */
function validate_change_password(string $currentPassword, string $newPassword, string $confirmPassword): array
{
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
 * Validate question form fields (add/edit).
 *
 * @param string $question
 * @param string $category
 * @param string $difficulty
 * @param string $option1
 * @param string $option2
 * @param string $option3
 * @param string $option4
 * @param int    $correct
 * @return string[] Array of error messages (empty if valid).
 */
function validate_question(
    string $question,
    string $category,
    string $difficulty,
    string $option1,
    string $option2,
    string $option3,
    string $option4,
    int $correct
): array {
    $errors = [];

    if ($question === '') {
        $errors[] = 'Question text is required.';
    }
    if ($category === '') {
        $errors[] = 'Category is required.';
    }
    if (!in_array($difficulty, ['Easy', 'Medium', 'Hard'], true)) {
        $errors[] = 'Select a valid difficulty.';
    }
    if ($option1 === '' || $option2 === '' || $option3 === '' || $option4 === '') {
        $errors[] = 'All options are required.';
    }
    if ($correct < 1 || $correct > 4) {
        $errors[] = 'Select a valid correct option.';
    }

    return $errors;
}
