<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Quiz;
use App\QuestionTypes\QuestionTypeRegistry;
use Illuminate\Http\Request;

class AttemptController extends Controller
{
    public function __construct(private QuestionTypeRegistry $registry) {}

    public function start(Quiz $quiz)
    {
        abort_if($quiz->questions()->count() === 0, 404, 'This quiz has no questions yet.');

        $quiz->load('questions.options');

        $attempt = $quiz->attempts()->create([
            'started_at' => now(),
            'max_score' => $quiz->totalMarks(),
        ]);

        return view('attempts.take', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'registry' => $this->registry,
        ]);
    }

    public function submit(Request $request, Attempt $attempt)
    {
        abort_if($attempt->isSubmitted(), 403, 'This attempt has already been submitted.');

        $attempt->load('quiz.questions.options');
        $raw = (array) $request->input('answers', []);

        $total = 0.0;

        foreach ($attempt->quiz->questions as $question) {
            $type = $this->registry->for($question->type);
            $payload = $type->normalizePayload($raw[$question->id] ?? null);
            $marks = $type->evaluate($question, $payload);

            $attempt->answers()->create([
                'question_id' => $question->id,
                'payload' => $payload,
                'marks_awarded' => $marks,
            ]);

            $total += $marks;
        }

        $attempt->update([
            'submitted_at' => now(),
            'score' => $total,
            'max_score' => $attempt->quiz->totalMarks(),
        ]);

        return redirect()->route('attempts.show', $attempt);
    }

    public function show(Attempt $attempt)
    {
        abort_unless($attempt->isSubmitted(), 404);

        $attempt->load('quiz', 'answers.question.options');

        return view('attempts.result', [
            'attempt' => $attempt,
            'registry' => $this->registry,
        ]);
    }
}
