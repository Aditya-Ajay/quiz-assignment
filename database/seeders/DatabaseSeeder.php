<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $quiz = Quiz::create([
            'title' => 'Sample Quiz — All Question Types',
            'description' => 'A demo quiz with one question of every supported type.',
        ]);

        $binary = $quiz->questions()->create([
            'type' => 'binary',
            'body_html' => '<p>Is the Earth round?</p>',
            'marks' => 1,
            'position' => 1,
            'config' => ['correct' => 'yes'],
        ]);

        $single = $quiz->questions()->create([
            'type' => 'single_choice',
            'body_html' => '<p>Which city is the <b>capital of France</b>?</p>',
            'marks' => 2,
            'position' => 2,
            'config' => [],
        ]);
        $single->options()->createMany([
            ['label' => 'London', 'is_correct' => false, 'position' => 0],
            ['label' => 'Paris', 'is_correct' => true, 'position' => 1],
            ['label' => 'Berlin', 'is_correct' => false, 'position' => 2],
        ]);

        $multi = $quiz->questions()->create([
            'type' => 'multiple_choice',
            'body_html' => '<p>Select all <i>prime numbers</i>:</p>',
            'marks' => 3,
            'position' => 3,
            'config' => ['scoring' => 'partial'],
        ]);
        foreach ([['2', true], ['3', true], ['4', false], ['5', true], ['6', false]] as $i => [$label, $correct]) {
            $multi->options()->create([
                'label' => $label,
                'is_correct' => $correct,
                'position' => $i,
            ]);
        }

        $quiz->questions()->create([
            'type' => 'number',
            'body_html' => '<p>What is 15 &times; 8?</p>',
            'marks' => 1,
            'position' => 4,
            'config' => ['expected' => 120, 'tolerance' => 0],
        ]);

        $quiz->questions()->create([
            'type' => 'text',
            'body_html' => '<p>Name the Laravel ORM.</p>',
            'marks' => 1,
            'position' => 5,
            'config' => ['expected' => 'Eloquent', 'match' => 'ci'],
        ]);
    }
}
