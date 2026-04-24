@extends('layouts.app')
@section('title', 'Quizzes')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-slate-900">Quizzes</h1>
    <a href="{{ route('quizzes.create') }}"
       class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
        + New Quiz
    </a>
</div>

@if ($quizzes->isEmpty())
    <div class="rounded-lg border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
        No quizzes yet. Create your first one.
    </div>
@else
    <div class="grid gap-4">
        @foreach ($quizzes as $quiz)
            <div class="rounded-lg border border-slate-200 bg-white p-5 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">{{ $quiz->title }}</h2>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ $quiz->questions_count }} {{ Str::plural('question', $quiz->questions_count) }}
                        @if ($quiz->description) · {{ $quiz->description }} @endif
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('quizzes.edit', $quiz) }}"
                       class="rounded-md border border-slate-300 px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50">
                        Edit
                    </a>
                    @if ($quiz->questions_count > 0)
                        <a href="{{ route('attempts.start', $quiz) }}"
                           class="rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700">
                            Take Quiz
                        </a>
                    @endif
                    <form method="POST" action="{{ route('quizzes.destroy', $quiz) }}"
                          onsubmit="return confirm('Delete this quiz and all its questions?')">
                        @csrf @method('DELETE')
                        <button class="rounded-md border border-rose-300 px-3 py-1.5 text-sm text-rose-700 hover:bg-rose-50">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
