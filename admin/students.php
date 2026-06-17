<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/html_head.php';
require_once '../includes/sidebar.php';

require_admin();

$stmt = $pdo->query(
    'SELECT s.id, s.name, s.email, s.created_at, 
            COALESCE(COUNT(r.id), 0) AS attempts
     FROM students s
     LEFT JOIN results r ON r.student_id = s.id
     GROUP BY s.id, s.name, s.email, s.created_at
     ORDER BY s.created_at DESC'
);
$students = $stmt->fetchAll();

render_html_head('Students');
?>
<div class="layout">
    <?php render_admin_sidebar('students.php'); ?>

    <main class="main-content">
        <div class="topbar">
            <div>
                <div class="topbar-title">Registered Students</div>
                <div class="topbar-subtitle">All students enrolled in the system.</div>
            </div>
        </div>

        <div class="section">
            <?php if (empty($students)): ?>
                <div class="alert alert-error">
                    No students registered yet.
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Attempts</th>
                            <th>Registered On</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td><?php echo (int)$s['id']; ?></td>
                                <td><?php echo htmlspecialchars($s['name']); ?></td>
                                <td><?php echo htmlspecialchars($s['email']); ?></td>
                                <td><?php echo (int)$s['attempts']; ?></td>
                                <td><?php echo htmlspecialchars($s['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
