<?php
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $stmt = $pdo->query(
        'SELECT r.id, r.score, r.total_questions, r.percentage, r.created_at,
                s.name AS student_name, s.email AS student_email
         FROM results r
         JOIN students s ON r.student_id = s.id
         ORDER BY r.created_at DESC'
    );

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=quiz_results.csv');

    $output = fopen('php://output', 'w');
    if ($output === false) {
        throw new RuntimeException('Failed to open output stream for CSV export.');
    }

    // Header row
    fputcsv($output, ['ID', 'Student Name', 'Email', 'Score', 'Total Questions', 'Percentage', 'Attempted On']);

    while ($row = $stmt->fetch()) {
        fputcsv($output, [
            $row['id'],
            $row['student_name'],
            $row['student_email'],
            $row['score'],
            $row['total_questions'],
            $row['percentage'],
            $row['created_at']
        ]);
    }

    fclose($output);
} catch (Throwable $e) {
    app_log_error('Admin CSV export', $e);
    http_response_code(500);
    echo 'Export failed. Please try again later.';
}
exit;