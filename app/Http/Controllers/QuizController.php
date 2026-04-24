<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\QuestionTypes\QuestionTypeRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::withCount('questions')->latest()->get();

        return view('quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('quizzes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $quiz = Quiz::create($data);

        return redirect()->route('quizzes.edit', $quiz)
            ->with('status', 'Quiz created. Add your questions below.');
    }

    public function edit(Quiz $quiz, QuestionTypeRegistry $registry)
    {
        $quiz->load('questions.options');

        return view('quizzes.edit', [
            'quiz' => $quiz,
            'types' => $registry->options(),
            'registry' => $registry,
        ]);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $quiz->update($data);

        return redirect()->route('quizzes.edit', $quiz)->with('status', 'Quiz updated.');
    }

    public function destroy(Quiz $quiz)
    {
        foreach ($quiz->questions as $question) {
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }
            foreach ($question->options as $option) {
                if ($option->image_path) {
                    Storage::disk('public')->delete($option->image_path);
                }
            }
        }

        $quiz->delete();

        return redirect()->route('quizzes.index')->with('status', 'Quiz deleted.');
    }
}
