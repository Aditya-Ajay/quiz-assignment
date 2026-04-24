<?php

namespace App\QuestionTypes\Types;

use App\Models\Question;
use App\QuestionTypes\Contracts\QuestionType;

class SingleChoiceType implements QuestionType
{
    public function slug(): string
    {
        return 'single_choice';
    }

    public function label(): string
    {
        return 'Single Choice';
    }

    public function buildConfig(array $input): array
    {
        return [];
    }

    public function persistOptions(Question $question, array $input): void
    {
        $question->options()->delete();

        $labels = $input['options'] ?? [];
        $correctIndex = isset($input['correct_option']) ? (int) $input['correct_option'] : null;

        foreach ($labels as $i => $label) {
            if (trim((string) $label) === '') {
                continue;
            }

            $question->options()->create([
                'label' => $label,
                'is_correct' => $i === $correctIndex,
                'position' => $i,
            ]);
        }
    }

    public function normalizePayload(mixed $raw): array
    {
        return ['option_id' => is_numeric($raw) ? (int) $raw : null];
    }

    public function evaluate(Question $question, array $payload): float
    {
        $pickedId = $payload['option_id'] ?? null;
        if ($pickedId === null) {
            return 0.0;
        }

        $picked = $question->options->firstWhere('id', $pickedId);

        return $picked && $picked->is_correct ? (float) $question->marks : 0.0;
    }

    public function editorView(): string
    {
        return 'questions.editor.single_choice';
    }

    public function inputView(): string
    {
        return 'questions.input.single_choice';
    }

    public function reviewView(): string
    {
        return 'questions.review.single_choice';
    }
}
