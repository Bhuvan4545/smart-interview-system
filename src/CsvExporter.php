<?php

namespace SmartInterview;

class CsvExporter
{
    /**
     * Write result rows as CSV to a stream.
     *
     * @param resource $output  An open writable stream (e.g. fopen('php://output','w')).
     * @param array<int,array<string,mixed>> $rows  Result rows from the database.
     * @return int Number of data rows written.
     */
    public static function writeResults($output, array $rows): int
    {
        fputcsv($output, [
            'ID',
            'Student Name',
            'Email',
            'Score',
            'Total Questions',
            'Percentage',
            'Attempted On',
        ]);

        $count = 0;
        foreach ($rows as $row) {
            fputcsv($output, [
                $row['id']           ?? '',
                $row['student_name'] ?? '',
                $row['student_email'] ?? '',
                $row['score']        ?? '',
                $row['total_questions'] ?? '',
                $row['percentage']   ?? '',
                $row['created_at']   ?? '',
            ]);
            $count++;
        }

        return $count;
    }
}
