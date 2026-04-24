@extends('layouts.app')
@section('title', $quiz->title)

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-slate-900">{{ $quiz->title }}</h1>
    @if ($quiz->description)
        <p class="text-slate-600 mt-1">{{ $quiz->description }}</p>
    @endif
    <p class="text-sm text-slate-500 mt-2">
        {{ $quiz->questions->count() }} {{ Str::plural('question', $quiz->questions->count()) }} · Total marks: {{ $quiz->totalMarks() }}
    </p>
</div>

<form method="POST" action="{{ route('attempts.submit', $attempt) }}" class="space-y-5">
    @csrf

    @foreach ($quiz->questions as $i => $question)
        @php $type = $registry->for($question->type); @endphp

        <div class="bg-white border border-slate-200 rounded-lg p-6">
            <div class="flex items-start justify-between gap-4 mb-3">
                <h2 class="font-semibold text-slate-900">
                    Q{{ $i + 1 }}.
                    <span class="prose-quiz font-normal">{!! $question->body_html !!}</span>
                </h2>
                <span class="text-xs text-slate-500 shrink-0">
                    {{ $question->marks }} {{ Str::plural('mark', $question->marks) }}
                </span>
            </div>

            @if ($question->image_path)
                <img src="{{ Storage::url($question->image_path) }}" class="max-h-64 rounded mb-3">
            @endif

            @if ($question->video_url)
                <div class="mb-3 aspect-video">
                    {!! \App\Support\Youtube::embed($question->video_url) !!}
                </div>
            @endif

            @include($type->inputView(), ['question' => $question])
        </div>
    @endforeach

    <div class="flex items-center gap-3">
        <button class="rounded-md bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-700">
            Submit Quiz
        </button>
        <a href="{{ route('quizzes.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
    </div>
</form>
@endsection
