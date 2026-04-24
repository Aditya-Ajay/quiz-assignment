<?php

namespace App\QuestionTypes\Types;

use App\Models\Question;
use App\QuestionTypes\Contracts\QuestionType;

class BinaryType implements QuestionType
{
    public function slug(): string
    {
        return 'binary';
    }

    public function label(): string
    {
        return 'Binary (Yes / No)';
    }

    public function buildConfig(array $input): array
    {
        $correct = $input['binary_correct'] ?? 'yes';

        return ['correct' => $correct === 'no' ? 'no' : 'yes'];
    }

    public function persistOptions(Question $question, array $input): void
    {
        $question->options()->delete();
    }

    public function normalizePayload(mixed $raw): array
    {
        return ['value' => $raw === 'no' ? 'no' : ($raw === 'yes' ? 'yes' : null)];
    }

    public function evaluate(Question $question, array $payload): float
    {
        $expected = $question->config['correct'] ?? 'yes';

        return ($payload['value'] ?? null) === $expected ? (float) $question->marks : 0.0;
    }

    public function editorView(): string
    {
        return 'questions.editor.binary';
    }

    public function inputView(): string
    {
        return 'questions.input.binary';
    }

    public function reviewView(): string
    {
        return 'questions.review.binary';
    }
}
