<?php
/**
 * Render the common HTML <head> section.
 *
 * @param string   $title       Page title.
 * @param string[] $extraScripts Optional script paths to include (with defer).
 */
function render_html_head(string $title, array $extraScripts = []): void
{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($title); ?> - Smart Interview System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
<?php foreach ($extraScripts as $script): ?>
    <script src="<?php echo htmlspecialchars($script); ?>" defer></script>
<?php endforeach; ?>
</head>
<body>
<?php
}
