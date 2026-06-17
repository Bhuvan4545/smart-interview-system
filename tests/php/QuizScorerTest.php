<?php

namespace SmartInterview\Tests;

use PHPUnit\Framework\TestCase;
use SmartInterview\QuizScorer;

class QuizScorerTest extends TestCase
{
    public function testPerfectScore(): void
    {
        $questions = [
            ['id' => 1, 'correct_option' => 2],
            ['id' => 2, 'correct_option' => 3],
            ['id' => 3, 'correct_option' => 1],
        ];
        $answers = [1 => 2, 2 => 3, 3 => 1];

        $result = QuizScorer::score($answers, $questions);

        $this->assertSame(3, $result['score']);
        $this->assertSame(3, $result['total']);
        $this->assertSame(100.0, $result['percentage']);
    }

    public function testZeroScore(): void
    {
        $questions = [
            ['id' => 1, 'correct_option' => 2],
            ['id' => 2, 'correct_option' => 3],
        ];
        $answers = [1 => 1, 2 => 1];

        $result = QuizScorer::score($answers, $questions);

        $this->assertSame(0, $result['score']);
        $this->assertSame(2, $result['total']);
        $this->assertSame(0.0, $result['percentage']);
    }

    public function testPartialScore(): void
    {
        $questions = [
            ['id' => 1, 'correct_option' => 2],
            ['id' => 2, 'correct_option' => 3],
            ['id' => 3, 'correct_option' => 4],
            ['id' => 4, 'correct_option' => 1],
        ];
        $answers = [1 => 2, 2 => 1, 3 => 4, 4 => 4];

        $result = QuizScorer::score($answers, $questions);

        $this->assertSame(2, $result['score']);
        $this->assertSame(4, $result['total']);
        $this->assertSame(50.0, $result['percentage']);
    }

    public function testEmptyQuestions(): void
    {
        $result = QuizScorer::score([], []);

        $this->assertSame(0, $result['score']);
        $this->assertSame(0, $result['total']);
        $this->assertSame(0.0, $result['percentage']);
    }

    public function testUnansweredQuestionsCountAsWrong(): void
    {
        $questions = [
            ['id' => 1, 'correct_option' => 2],
            ['id' => 2, 'correct_option' => 3],
            ['id' => 3, 'correct_option' => 1],
        ];
        $answers = [1 => 2]; // only answered Q1

        $result = QuizScorer::score($answers, $questions);

        $this->assertSame(1, $result['score']);
        $this->assertSame(3, $result['total']);
        $this->assertSame(33.33, $result['percentage']);
    }

    public function testExtraAnswersAreIgnored(): void
    {
        $questions = [
            ['id' => 1, 'correct_option' => 2],
        ];
        $answers = [1 => 2, 99 => 1, 100 => 3];

        $result = QuizScorer::score($answers, $questions);

        $this->assertSame(1, $result['score']);
        $this->assertSame(1, $result['total']);
        $this->assertSame(100.0, $result['percentage']);
    }

    public function testStringTypeCoercionInAnswers(): void
    {
        $questions = [
            ['id' => '1', 'correct_option' => '2'],
            ['id' => '2', 'correct_option' => '3'],
        ];
        $answers = ['1' => '2', '2' => '1'];

        $result = QuizScorer::score($answers, $questions);

        $this->assertSame(1, $result['score']);
    }

    public function testSingleQuestion(): void
    {
        $questions = [['id' => 42, 'correct_option' => 4]];
        $answers = [42 => 4];

        $result = QuizScorer::score($answers, $questions);

        $this->assertSame(1, $result['score']);
        $this->assertSame(1, $result['total']);
        $this->assertSame(100.0, $result['percentage']);
    }

    public function testPercentageRounding(): void
    {
        $questions = [
            ['id' => 1, 'correct_option' => 1],
            ['id' => 2, 'correct_option' => 1],
            ['id' => 3, 'correct_option' => 1],
        ];
        $answers = [1 => 1]; // 1 out of 3 = 33.33%

        $result = QuizScorer::score($answers, $questions);
        $this->assertSame(33.33, $result['percentage']);
    }
}
