<?php

namespace App\QuestionTypes\Contracts;

use App\Models\Question;

interface QuestionType
{
    public function slug(): string;

    public function label(): string;

    public function buildConfig(array $input): array;

    public function persistOptions(Question $question, array $input): void;

    public function normalizePayload(mixed $raw): array;

    public function evaluate(Question $question, array $payload): float;

    public function editorView(): string;

    public function inputView(): string;

    public function reviewView(): string;
}
