<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QuizGenerationController extends Controller
{
    private string $ragServiceUrl;

    public function __construct()
    {
        $this->ragServiceUrl = config('services.rag.url', 'http://localhost:8001');
    }

    public function generate(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'count' => 'required|integer|min:1|max:20',
            'types' => 'required|array',
            'types.*' => 'in:binary,single_choice,multiple_choice,number,text',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        try {
            $response = Http::timeout(60)->post("{$this->ragServiceUrl}/generate", $validated);

            if (!$response->successful()) {
                throw new \Exception($response->json('detail') ?? 'RAG service error');
            }

            $data = $response->json();
            $sourceChunks = collect($data['source_chunks'])->pluck('content')->all();

            foreach ($data['questions'] as $q) {
                $config = $q['config'] ?? [];
                $config['rag_enabled'] = true;
                $config['source_chunks'] = $sourceChunks;

                $question = $quiz->questions()->create([
                    'type' => $q['type'],
                    'body_html' => $q['text'],
                    'marks' => $q['points'] ?? 10,
                    'config' => $config,
                ]);

                if (isset($q['options'])) {
                    foreach ($q['options'] as $opt) {
                        $question->options()->create([
                            'label' => $opt['text'],
                            'is_correct' => $opt['is_correct'] ?? false,
                        ]);
                    }
                }
            }

            return redirect()
                ->route('quizzes.edit', $quiz)
                ->with('success', count($data['questions']) . ' AI questions generated');
        } catch (\Exception $e) {
            return redirect()
                ->route('quizzes.edit', $quiz)
                ->with('error', 'Generation failed: ' . $e->getMessage());
        }
    }
}
