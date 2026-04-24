<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use App\QuestionTypes\QuestionTypeRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function __construct(private QuestionTypeRegistry $registry) {}

    public function store(Request $request, Quiz $quiz)
    {
        $base = $this->validateBase($request);
        $type = $this->registry->for($base['type']);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('questions', 'public')
            : null;

        $question = $quiz->questions()->create([
            'type' => $base['type'],
            'body_html' => $base['body_html'],
            'image_path' => $imagePath,
            'video_url' => $base['video_url'],
            'marks' => $base['marks'],
            'position' => $quiz->questions()->max('position') + 1,
            'config' => $type->buildConfig($request->all()),
        ]);

        $type->persistOptions($question, $request->all());
        $this->persistOptionImages($question, $request);

        return redirect()->route('quizzes.edit', $quiz)->with('status', 'Question added.');
    }

    public function edit(Question $question)
    {
        $question->load('options');

        return view('questions.edit', [
            'question' => $question,
            'type' => $this->registry->for($question->type),
        ]);
    }

    public function update(Request $request, Question $question)
    {
        $base = $this->validateBase($request, isUpdate: true);
        $type = $this->registry->for($question->type);

        if ($request->hasFile('image')) {
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }
            $question->image_path = $request->file('image')->store('questions', 'public');
        } elseif ($request->boolean('remove_image') && $question->image_path) {
            Storage::disk('public')->delete($question->image_path);
            $question->image_path = null;
        }

        $question->update([
            'body_html' => $base['body_html'],
            'video_url' => $base['video_url'],
            'marks' => $base['marks'],
            'image_path' => $question->image_path,
            'config' => $type->buildConfig($request->all()),
        ]);

        $type->persistOptions($question, $request->all());
        $this->persistOptionImages($question->fresh('options'), $request);

        return redirect()->route('quizzes.edit', $question->quiz_id)->with('status', 'Question updated.');
    }

    public function destroy(Question $question)
    {
        $quizId = $question->quiz_id;

        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }
        foreach ($question->options as $option) {
            if ($option->image_path) {
                Storage::disk('public')->delete($option->image_path);
            }
        }

        $question->delete();

        return redirect()->route('quizzes.edit', $quizId)->with('status', 'Question removed.');
    }

    private function validateBase(Request $request, bool $isUpdate = false): array
    {
        $rules = [
            'body_html' => ['required', 'string'],
            'video_url' => ['nullable', 'url'],
            'marks' => ['required', 'integer', 'min:1'],
            'image' => ['nullable', 'image', 'max:4096'],
        ];

        if (! $isUpdate) {
            $rules['type'] = ['required', 'string', 'in:'.implode(',', array_keys($this->registry->all()))];
        }

        return $request->validate($rules);
    }

    private function persistOptionImages(Question $question, Request $request): void
    {
        $files = $request->file('option_images') ?? [];
        $question->loadMissing('options');

        foreach ($question->options as $index => $option) {
            if (isset($files[$index]) && $files[$index]) {
                if ($option->image_path) {
                    Storage::disk('public')->delete($option->image_path);
                }
                $option->update([
                    'image_path' => $files[$index]->store('options', 'public'),
                ]);
            }
        }
    }
}
