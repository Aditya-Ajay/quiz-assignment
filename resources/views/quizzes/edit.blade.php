@extends('layouts.app')
@section('title', 'Edit Quiz')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-slate-900">{{ $quiz->title }}</h1>
    <div class="flex gap-2">
        @if ($quiz->questions->isNotEmpty())
            <a href="{{ route('attempts.start', $quiz) }}"
               class="rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700">
                Take Quiz
            </a>
        @endif
        <a href="{{ route('quizzes.index') }}" class="text-sm text-slate-600 hover:text-slate-900 px-3 py-1.5">Back</a>
    </div>
</div>

<section class="bg-white border border-slate-200 rounded-lg p-6 mb-8">
    <h2 class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-3">Quiz details</h2>
    <form method="POST" action="{{ route('quizzes.update', $quiz) }}" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Title</label>
            <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required
                   class="w-full rounded-md border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
            <textarea name="description" rows="2" class="w-full rounded-md border border-slate-300 px-3 py-2">{{ old('description', $quiz->description) }}</textarea>
        </div>
        <button class="rounded-md border border-slate-300 px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50">Save details</button>
    </form>
</section>

<section class="mb-8">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-semibold text-slate-900">Questions</h2>
        <div class="flex items-center gap-3">
            <span class="text-sm text-slate-500">Total marks: {{ $quiz->totalMarks() }}</span>
            <button onclick="document.getElementById('ai-modal').classList.remove('hidden')"
                    class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700">
                ✨ Generate with AI
            </button>
        </div>
    </div>

    @if ($quiz->questions->isEmpty())
        <div class="rounded-lg border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
            No questions yet. Add one below.
        </div>
    @else
        <div class="space-y-3">
            @foreach ($quiz->questions as $question)
                <div class="rounded-lg border border-slate-200 bg-white p-4 flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                            <span class="rounded bg-slate-100 px-2 py-0.5">{{ $types[$question->type] ?? $question->type }}</span>
                            <span>{{ $question->marks }} {{ Str::plural('mark', $question->marks) }}</span>
                        </div>
                        <div class="prose-quiz text-slate-800">{!! $question->body_html !!}</div>
                        @if ($question->options->isNotEmpty())
                            <ul class="mt-2 text-sm text-slate-600 space-y-0.5">
                                @foreach ($question->options as $opt)
                                    <li>
                                        @if ($opt->is_correct) <span class="text-emerald-600 font-medium">✓</span> @else · @endif
                                        {{ $opt->label }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('questions.edit', $question) }}"
                           class="rounded-md border border-slate-300 px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50">Edit</a>
                        <form method="POST" action="{{ route('questions.destroy', $question) }}"
                              onsubmit="return confirm('Delete this question?')">
                            @csrf @method('DELETE')
                            <button class="rounded-md border border-rose-300 px-3 py-1.5 text-sm text-rose-700 hover:bg-rose-50">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

<section class="bg-white border border-slate-200 rounded-lg p-6">
    <h2 class="text-lg font-semibold text-slate-900 mb-4">Add a question</h2>

    <form method="POST" action="{{ route('questions.store', $quiz) }}" enctype="multipart/form-data" id="add-question-form" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
            <select name="type" id="type-select" class="w-full rounded-md border border-slate-300 px-3 py-2">
                @foreach ($types as $slug => $label)
                    <option value="{{ $slug }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        @include('questions._shared_fields', ['question' => null])

        @foreach ($types as $slug => $label)
            <div data-type-panel="{{ $slug }}" class="type-panel">
                @include('questions.editor.'.$slug, ['question' => null])
            </div>
        @endforeach

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Add question</button>
        </div>
    </form>

    <script>
        (function () {
            const select = document.getElementById('type-select');
            const panels = document.querySelectorAll('[data-type-panel]');
            function sync() {
                panels.forEach(p => p.style.display = p.dataset.typePanel === select.value ? '' : 'none');
            }
            select.addEventListener('change', sync);
            sync();
        })();
    </script>
</section>

<!-- AI Generation Modal -->
<div id="ai-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
        <div class="flex items-center justify-between p-6 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900">✨ Generate Questions with AI</h3>
            <button onclick="document.getElementById('ai-modal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form method="POST" action="{{ route('quizzes.generate', $quiz) }}" class="p-6 space-y-4" id="ai-generate-form">
            @csrf
            
            <div id="generation-progress" class="hidden">
                <div class="flex items-center gap-3 p-4 bg-indigo-50 rounded-lg">
                    <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <div>
                        <div class="text-sm font-medium text-indigo-900">Generating questions...</div>
                        <div class="text-xs text-indigo-700">This may take 10-15 seconds</div>
                    </div>
                </div>
            </div>
            
            <div id="generation-form">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Topic</label>
                <input type="text" name="topic" required placeholder="e.g., Wozku's advocacy-led growth model"
                       class="w-full rounded-md border border-slate-300 px-3 py-2">
                <p class="text-xs text-slate-500 mt-1">Questions will be generated from Wozku's knowledge base</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Number of questions</label>
                <input type="number" name="count" value="5" min="1" max="20" required
                       class="w-full rounded-md border border-slate-300 px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Question types</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="types[]" value="single_choice" checked class="rounded">
                        <span class="text-sm text-slate-700">Single Choice</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="types[]" value="multiple_choice" checked class="rounded">
                        <span class="text-sm text-slate-700">Multiple Choice</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="types[]" value="text" checked class="rounded">
                        <span class="text-sm text-slate-700">Text</span>
                    </label>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Difficulty</label>
                <select name="difficulty" required class="w-full rounded-md border border-slate-300 px-3 py-2">
                    <option value="easy">Easy</option>
                    <option value="medium" selected>Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </div>
            
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Generate Questions
                </button>
                <button type="button" onclick="document.getElementById('ai-modal').classList.add('hidden')"
                        class="rounded-md border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    Cancel
                </button>
            </div>
            </div>
        </form>
        
        <script>
            document.getElementById('ai-generate-form').addEventListener('submit', function() {
                document.getElementById('generation-form').classList.add('hidden');
                document.getElementById('generation-progress').classList.remove('hidden');
            });
        </script>
    </div>
</div>
@endsection
