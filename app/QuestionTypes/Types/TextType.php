<?php

namespace App\QuestionTypes\Types;

use App\Models\Question;
use App\QuestionTypes\Contracts\QuestionType;

class TextType implements QuestionType
{
    public function slug(): string
    {
        return 'text';
    }

    public function label(): string
    {
        return 'Text Input';
    }

    public function buildConfig(array $input): array
    {
        $match = $input['match'] ?? 'ci';
        $allowed = ['exact', 'ci', 'contains', 'regex'];

        return [
            'expected' => (string) ($input['expected'] ?? ''),
            'match' => in_array($match, $allowed, true) ? $match : 'ci',
        ];
    }

    public function persistOptions(Question $question, array $input): void
    {
        $question->options()->delete();
    }

    public function normalizePayload(mixed $raw): array
    {
        return ['value' => trim((string) ($raw ?? ''))];
    }

    public function evaluate(Question $question, array $payload): float
    {
        $given = (string) ($payload['value'] ?? '');
        if ($given === '') {
            return 0.0;
        }

        $expected = (string) ($question->config['expected'] ?? '');
        $mode = $question->config['match'] ?? 'ci';

        $match = match ($mode) {
            'exact' => $given === $expected,
            'contains' => $expected !== '' && stripos($given, $expected) !== false,
            'regex' => $expected !== '' && @preg_match($expected, $given) === 1,
            default => strcasecmp($given, $expected) === 0,
        };

        return $match ? (float) $question->marks : 0.0;
    }

    public function editorView(): string
    {
        return 'questions.editor.text';
    }

    public function inputView(): string
    {
        return 'questions.input.text';
    }

    public function reviewView(): string
    {
        return 'questions.review.text';
    }
}
