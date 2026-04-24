<?php

namespace App\QuestionTypes;

use App\QuestionTypes\Contracts\QuestionType;
use RuntimeException;

class QuestionTypeRegistry
{
    /** @var array<string, QuestionType> */
    private array $types = [];

    public function register(QuestionType $type): self
    {
        $this->types[$type->slug()] = $type;

        return $this;
    }

    public function for(string $slug): QuestionType
    {
        if (! isset($this->types[$slug])) {
            throw new RuntimeException("Unknown question type: {$slug}");
        }

        return $this->types[$slug];
    }

    /** @return array<string, QuestionType> */
    public function all(): array
    {
        return $this->types;
    }

    /** @return array<string, string> */
    public function options(): array
    {
        $out = [];
        foreach ($this->types as $slug => $type) {
            $out[$slug] = $type->label();
        }

        return $out;
    }
}
