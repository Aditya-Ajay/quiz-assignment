@extends('layouts.app')
@section('title', 'Edit Question')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-slate-900">Edit question</h1>
    <a href="{{ route('quizzes.edit', $question->quiz_id) }}" class="text-sm text-slate-600 hover:text-slate-900">Back to quiz</a>
</div>

<form method="POST" action="{{ route('questions.update', $question) }}"
      enctype="multipart/form-data"
      class="bg-white border border-slate-200 rounded-lg p-6 space-y-4">
    @csrf @method('PUT')

    <div class="text-sm text-slate-500">
        Type: <span class="rounded bg-slate-100 px-2 py-0.5">{{ $type->label() }}</span>
    </div>

    @include('questions._shared_fields', ['question' => $question])
    @include('questions.editor.'.$question->type, ['question' => $question])

    <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
        <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Save changes</button>
        <a href="{{ route('quizzes.edit', $question->quiz_id) }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
    </div>
</form>
@endsection
