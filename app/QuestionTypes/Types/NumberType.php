<?php

namespace App\QuestionTypes\Types;

use App\Models\Question;
use App\QuestionTypes\Contracts\QuestionType;

class NumberType implements QuestionType
{
    public function slug(): string
    {
        return 'number';
    }

    public function label(): string
    {
        return 'Number Input';
    }

    public function buildConfig(array $input): array
    {
        return [
            'expected' => isset($input['expected']) ? (float) $input['expected'] : 0.0,
            'tolerance' => isset($input['tolerance']) ? (float) $input['tolerance'] : 0.0,
        ];
    }

    public function persistOptions(Question $question, array $input): void
    {
        $question->options()->delete();
    }

    public function normalizePayload(mixed $raw): array
    {
        return ['value' => is_numeric($raw) ? (float) $raw : null];
    }

    public function evaluate(Question $question, array $payload): float
    {
        $value = $payload['value'] ?? null;
        if ($value === null) {
            return 0.0;
        }

        $expected = (float) ($question->config['expected'] ?? 0);
        $tolerance = (float) ($question->config['tolerance'] ?? 0);

        return abs($value - $expected) <= $tolerance ? (float) $question->marks : 0.0;
    }

    public function editorView(): string
    {
        return 'questions.editor.number';
    }

    public function inputView(): string
    {
        return 'questions.input.number';
    }

    public function reviewView(): string
    {
        return 'questions.review.number';
    }
}
