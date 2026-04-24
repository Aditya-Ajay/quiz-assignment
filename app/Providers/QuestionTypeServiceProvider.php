<?php

namespace App\Providers;

use App\QuestionTypes\QuestionTypeRegistry;
use App\QuestionTypes\Types\BinaryType;
use App\QuestionTypes\Types\MultipleChoiceType;
use App\QuestionTypes\Types\NumberType;
use App\QuestionTypes\Types\SingleChoiceType;
use App\QuestionTypes\Types\TextType;
use Illuminate\Support\ServiceProvider;

class QuestionTypeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QuestionTypeRegistry::class, function () {
            return (new QuestionTypeRegistry)
                ->register(new BinaryType)
                ->register(new SingleChoiceType)
                ->register(new MultipleChoiceType)
                ->register(new NumberType)
                ->register(new TextType);
        });
    }
}
