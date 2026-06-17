<?php

namespace SmartInterview;

class QuizScorer
{
    /**
     * Calculate the score for a quiz attempt.
     *
     * @param array<int,int> $answers      Map of question_id => selected_option (1-4).
     * @param array<int,array{id:int,correct_option:int}> $questions  List of question rows.
     * @return array{score:int,total:int,percentage:float}
     */
    public static function score(array $answers, array $questions): array
    {
        $total = count($questions);
        $score = 0;

        foreach ($questions as $q) {
            $qid = (int) $q['id'];
            $correct = (int) $q['correct_option'];
            if (isset($answers[$qid]) && (int) $answers[$qid] === $correct) {
                $score++;
            }
        }

        $percentage = $total > 0 ? ($score / $total) * 100 : 0.0;

        return [
            'score'      => $score,
            'total'      => $total,
            'percentage' => round($percentage, 2),
        ];
    }
}
