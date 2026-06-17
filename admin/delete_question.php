<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/html_head.php';
require_once '../includes/sidebar.php';

require_admin();

// Handle delete
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    if ($id > 0) {
        $del = $pdo->prepare('DELETE FROM questions WHERE id = ?');
        $del->execute([$id]);
    }
    header('Location: delete_question.php');
    exit;
}

$stmt = $pdo->query('SELECT * FROM questions ORDER BY id DESC');
$questions = $stmt->fetchAll();

render_html_head('Manage Questions');
?>
<div class="layout">
    <?php render_admin_sidebar('delete_question.php'); ?>

    <main class="main-content">
        <div class="topbar">
            <div>
                <div class="topbar-title">Manage Question Bank</div>
                <div class="topbar-subtitle">Review and delete questions from the system.</div>
            </div>
        </div>

        <div class="section">
            <?php if (empty($questions)): ?>
                <div class="alert alert-error">
                    No questions found. Add some from the Add Question page.
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Category</th>
                            <th>Difficulty</th>
                            <th>Correct</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($questions as $q): ?>
                            <tr>
                                <td><?php echo (int)$q['id']; ?></td>
                                <td><?php echo htmlspecialchars(mb_strimwidth($q['question'], 0, 80, '...')); ?></td>
                                <td><?php echo htmlspecialchars($q['category']); ?></td>
                                <td><?php echo htmlspecialchars($q['difficulty']); ?></td>
                                <td><?php echo (int)$q['correct_option']; ?></td>
                                <td>
                                    <a href="edit_question.php?id=<?php echo (int)$q['id']; ?>" class="btn btn-outline">
                                        Edit
                                    </a>
                                    <a
                                        href="delete_question.php?delete_id=<?php echo (int)$q['id']; ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this question?');"
                                    >
                                        Delete
                                    </a>
                                </td>
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
