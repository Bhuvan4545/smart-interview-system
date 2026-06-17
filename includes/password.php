<?php
/**
 * Shared password-change logic.
 */

/**
 * Process a password change request.
 *
 * @param PDO    $pdo            Database connection.
 * @param string $table          Table name ('admin' or 'students').
 * @param int    $userId         The ID of the user changing their password.
 * @param string $currentPassword The current (plain) password provided.
 * @param string $newPassword    The new password.
 * @param string $confirmPassword Confirmation of the new password.
 * @return array{errors: string[], success: string}
 */
function process_change_password(
    PDO $pdo,
    string $table,
    int $userId,
    string $currentPassword,
    string $newPassword,
    string $confirmPassword
): array {
    $errors = validate_change_password($currentPassword, $newPassword, $confirmPassword);

    $success = '';

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT password FROM $table WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($currentPassword, $row['password'])) {
            $errors[] = 'Current password is incorrect.';
        } else {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE $table SET password = ? WHERE id = ?");
            $update->execute([$newHash, $userId]);
            $success = 'Password changed successfully.';
        }
    }

    return ['errors' => $errors, 'success' => $success];
}
