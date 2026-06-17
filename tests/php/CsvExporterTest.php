<?php

namespace SmartInterview\Tests;

use PHPUnit\Framework\TestCase;
use SmartInterview\CsvExporter;

class CsvExporterTest extends TestCase
{
    public function testWriteResultsProducesCorrectCsv(): void
    {
        $rows = [
            [
                'id'              => 1,
                'student_name'    => 'Alice',
                'student_email'   => 'alice@example.com',
                'score'           => 8,
                'total_questions' => 10,
                'percentage'      => 80.00,
                'created_at'      => '2026-01-15 10:30:00',
            ],
            [
                'id'              => 2,
                'student_name'    => 'Bob',
                'student_email'   => 'bob@example.com',
                'score'           => 6,
                'total_questions' => 10,
                'percentage'      => 60.00,
                'created_at'      => '2026-01-16 11:00:00',
            ],
        ];

        $stream = fopen('php://memory', 'r+');
        $count = CsvExporter::writeResults($stream, $rows);
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        $this->assertSame(2, $count);

        $lines = array_filter(explode("\n", trim($csv)));
        $this->assertCount(3, $lines); // header + 2 data rows

        $this->assertStringContainsString('ID', $lines[0]);
        $this->assertStringContainsString('Student Name', $lines[0]);
        $this->assertStringContainsString('Alice', $lines[1]);
        $this->assertStringContainsString('Bob', $lines[2]);
    }

    public function testWriteResultsWithEmptyRows(): void
    {
        $stream = fopen('php://memory', 'r+');
        $count = CsvExporter::writeResults($stream, []);
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        $this->assertSame(0, $count);

        $lines = array_filter(explode("\n", trim($csv)));
        $this->assertCount(1, $lines); // header only
    }

    public function testWriteResultsHandlesMissingFields(): void
    {
        $rows = [
            [
                'id'    => 3,
                'score' => 5,
                // other fields missing
            ],
        ];

        $stream = fopen('php://memory', 'r+');
        $count = CsvExporter::writeResults($stream, $rows);
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        $this->assertSame(1, $count);
        $this->assertStringContainsString('3', $csv);
        $this->assertStringContainsString('5', $csv);
    }

    public function testCsvHeaderColumns(): void
    {
        $stream = fopen('php://memory', 'r+');
        CsvExporter::writeResults($stream, []);
        rewind($stream);
        $header = fgetcsv($stream);
        fclose($stream);

        $expected = ['ID', 'Student Name', 'Email', 'Score', 'Total Questions', 'Percentage', 'Attempted On'];
        $this->assertSame($expected, $header);
    }

    public function testCsvSpecialCharactersAreEscaped(): void
    {
        $rows = [
            [
                'id'              => 1,
                'student_name'    => 'O\'Brien, "Bob"',
                'student_email'   => 'bob@example.com',
                'score'           => 7,
                'total_questions' => 10,
                'percentage'      => 70.00,
                'created_at'      => '2026-01-17 12:00:00',
            ],
        ];

        $stream = fopen('php://memory', 'r+');
        CsvExporter::writeResults($stream, $rows);
        rewind($stream);

        // skip header
        fgetcsv($stream);
        $dataRow = fgetcsv($stream);
        fclose($stream);

        $this->assertSame('O\'Brien, "Bob"', $dataRow[1]);
    }
}
