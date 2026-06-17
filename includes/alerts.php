<?php
/**
 * Render error and success alert blocks.
 *
 * @param string[] $errors  Array of error messages.
 * @param string   $success Success message (empty string if none).
 */
function render_alerts(array $errors, string $success = ''): void
{
    if (!empty($errors)):
?>
            <div class="alert alert-error">
<?php foreach ($errors as $e): ?>
                <div><?php echo htmlspecialchars($e); ?></div>
<?php endforeach; ?>
            </div>
<?php
    endif;

    if ($success !== ''):
?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
<?php
    endif;
}
