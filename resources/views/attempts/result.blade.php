@extends('layouts.app')
@section('title', 'Result — '.$attempt->quiz->title)

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-slate-900">Quiz Result</h1>
    <p class="text-slate-600 mt-1">{{ $attempt->quiz->title }}</p>
</div>

<div class="bg-white border border-slate-200 rounded-lg p-8 mb-6 text-center">
    <div class="text-sm text-slate-500 uppercase tracking-wide mb-2">You scored</div>
    <div class="text-5xl font-bold text-slate-900">
        {{ rtrim(rtrim(number_format($attempt->score, 2), '0'), '.') }}
        <span class="text-slate-400">/ {{ (int) $attempt->max_score }}</span>
    </div>
    @if ($attempt->percentage() !== null)
        <div class="text-lg text-slate-600 mt-2">{{ $attempt->percentage() }}%</div>
    @endif
</div>

<div class="space-y-4">
    @foreach ($attempt->answers as $i => $answer)
        @php
            $question = $answer->question;
            $type = $registry->for($question->type);
            $isFull = (float) $answer->marks_awarded >= (float) $question->marks;
            $isZero = (float) $answer->marks_awarded <= 0;
            $tone = $isFull ? 'emerald' : ($isZero ? 'rose' : 'amber');
        @endphp

        <div class="bg-white border border-slate-200 rounded-lg p-5">
            <div class="flex items-start justify-between gap-4 mb-2">
                <div class="font-medium text-slate-900">
                    Q{{ $i + 1 }}. <span class="font-normal prose-quiz">{!! $question->body_html !!}</span>
                </div>
                <div class="text-sm shrink-0 text-{{ $tone }}-700 font-medium">
                    {{ $isFull ? '✓' : ($isZero ? '✗' : '½') }}
                    +{{ rtrim(rtrim(number_format($answer->marks_awarded, 2), '0'), '.') }} / {{ $question->marks }}
                </div>
            </div>

            @include($type->reviewView(), ['question' => $question, 'answer' => $answer])
        </div>
    @endforeach
</div>

<div class="mt-6">
    <a href="{{ route('quizzes.index') }}"
       class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
        Back to quizzes
    </a>
</div>
@endsection
