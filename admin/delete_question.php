<?php
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Handle delete via POST (not GET) to prevent CSRF via link/image preloads
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!csrf_validate()) {
        die('Invalid form submission.');
    }
    $id = (int)$_POST['delete_id'];
    if ($id > 0) {
        $del = $pdo->prepare('DELETE FROM questions WHERE id = ?');
        $del->execute([$id]);
    }
    header('Location: delete_question.php');
    exit;
}

$stmt = $pdo->query('SELECT * FROM questions ORDER BY id DESC');
$questions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Questions - Smart Interview System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-logo">SI</div>
            <div>
                <div class="sidebar-title">Smart Interview</div>
                <div class="sidebar-subtitle">Admin Panel</div>
            </div>
        </div>
        <nav class="sidebar-menu">
            <div class="sidebar-section-title">Navigation</div>
            <a href="dashboard.php">
                <span class="label">Dashboard</span>
            </a>
            <a href="add_question.php">
                <span class="label">Add Question</span>
            </a>
            <a href="delete_question.php" class="active">
                <span class="label">Manage Questions</span>
            </a>
            <a href="students.php">
                <span class="label">View Students</span>
            </a>
            <a href="results.php">
                <span class="label">View Results</span>
            </a>

            <div class="sidebar-section-title">Account</div>
<a href="change_password.php">
    <span class="label">Change Password</span>
</a>
<a href="logout.php">
    <span class="label">Logout</span>
</a>
        </nav>
    </aside>

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
            <form method="post" action="" style="display:inline;">
                <?php csrf_field(); ?>
                <input type="hidden" name="delete_id" value="<?php echo (int)$q['id']; ?>">
                <button
                    type="submit"
                    class="btn btn-danger"
                    onclick="return confirm('Are you sure you want to delete this question?');"
                >
                    Delete
                </button>
            </form>
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
