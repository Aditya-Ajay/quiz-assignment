<?php

namespace App\QuestionTypes\Types;

use App\Models\Question;
use App\QuestionTypes\Contracts\QuestionType;

class MultipleChoiceType implements QuestionType
{
    public function slug(): string
    {
        return 'multiple_choice';
    }

    public function label(): string
    {
        return 'Multiple Choice';
    }

    public function buildConfig(array $input): array
    {
        $scoring = ($input['scoring'] ?? 'strict') === 'partial' ? 'partial' : 'strict';

        return ['scoring' => $scoring];
    }

    public function persistOptions(Question $question, array $input): void
    {
        $question->options()->delete();

        $labels = $input['options'] ?? [];
        $correctIndexes = array_map('intval', (array) ($input['correct_options'] ?? []));

        foreach ($labels as $i => $label) {
            if (trim((string) $label) === '') {
                continue;
            }

            $question->options()->create([
                'label' => $label,
                'is_correct' => in_array($i, $correctIndexes, true),
                'position' => $i,
            ]);
        }
    }

    public function normalizePayload(mixed $raw): array
    {
        $ids = array_values(array_unique(array_map('intval', (array) ($raw ?? []))));

        return ['option_ids' => $ids];
    }

    public function evaluate(Question $question, array $payload): float
    {
        $picked = $payload['option_ids'] ?? [];
        $correct = $question->options->where('is_correct', true)->pluck('id')->all();
        $total = count($correct);

        if ($total === 0) {
            return 0.0;
        }

        $scoring = $question->config['scoring'] ?? 'strict';

        if ($scoring === 'strict') {
            return array_values(array_intersect($picked, $correct)) === array_values($correct)
                && count($picked) === $total
                    ? (float) $question->marks
                    : 0.0;
        }

        $right = count(array_intersect($picked, $correct));
        $wrong = count(array_diff($picked, $correct));
        $score = max(0, $right - $wrong) / $total;

        return round($score * $question->marks, 2);
    }

    public function editorView(): string
    {
        return 'questions.editor.multiple_choice';
    }

    public function inputView(): string
    {
        return 'questions.input.multiple_choice';
    }

    public function reviewView(): string
    {
        return 'questions.review.multiple_choice';
    }
}
