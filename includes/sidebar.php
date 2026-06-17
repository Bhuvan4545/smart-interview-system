<?php
/**
 * Render the admin sidebar navigation.
 *
 * @param string $activePage Filename of the current page (e.g. 'dashboard.php').
 */
function render_admin_sidebar(string $activePage): void
{
    $navItems = [
        'dashboard.php'       => 'Dashboard',
        'add_question.php'    => 'Add Question',
        'delete_question.php' => 'Manage Questions',
        'students.php'        => 'View Students',
        'results.php'         => 'View Results',
    ];

    $accountItems = [
        'change_password.php' => 'Change Password',
        'logout.php'          => 'Logout',
    ];

    render_sidebar('Admin Panel', $navItems, $accountItems, $activePage);
}

/**
 * Render the student sidebar navigation.
 *
 * @param string $activePage Filename of the current page (e.g. 'dashboard.php').
 */
function render_student_sidebar(string $activePage): void
{
    $navItems = [
        'dashboard.php' => 'Dashboard',
        'quiz.php'      => 'Take Quiz',
        'result.php'    => 'Latest Result',
        'progress.php'  => 'Progress',
    ];

    $accountItems = [
        'change_password.php' => 'Change Password',
        'logout.php'          => 'Logout',
    ];

    render_sidebar('Student Panel', $navItems, $accountItems, $activePage);
}

/**
 * Render a sidebar with the given configuration.
 */
function render_sidebar(string $subtitle, array $navItems, array $accountItems, string $activePage): void
{
?>
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-logo">SI</div>
            <div>
                <div class="sidebar-title">Smart Interview</div>
                <div class="sidebar-subtitle"><?php echo htmlspecialchars($subtitle); ?></div>
            </div>
        </div>
        <nav class="sidebar-menu">
            <div class="sidebar-section-title">Navigation</div>
<?php foreach ($navItems as $href => $label): ?>
            <a href="<?php echo $href; ?>"<?php echo ($activePage === $href) ? ' class="active"' : ''; ?>>
                <span class="label"><?php echo htmlspecialchars($label); ?></span>
            </a>
<?php endforeach; ?>

            <div class="sidebar-section-title">Account</div>
<?php foreach ($accountItems as $href => $label): ?>
            <a href="<?php echo $href; ?>"<?php echo ($activePage === $href) ? ' class="active"' : ''; ?>>
                <span class="label"><?php echo htmlspecialchars($label); ?></span>
            </a>
<?php endforeach; ?>
        </nav>
    </aside>
<?php
}
