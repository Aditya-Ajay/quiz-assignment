@extends('layouts.app')
@section('title', 'New Quiz')

@section('content')
<h1 class="text-2xl font-semibold text-slate-900 mb-6">New Quiz</h1>

<form method="POST" action="{{ route('quizzes.store') }}" class="space-y-4 bg-white border border-slate-200 rounded-lg p-6">
    @csrf
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Title</label>
        <input type="text" name="title" value="{{ old('title') }}" required
               class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
        <textarea name="description" rows="3"
                  class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none">{{ old('description') }}</textarea>
    </div>
    <div class="flex items-center gap-3">
        <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Create</button>
        <a href="{{ route('quizzes.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
    </div>
</form>
@endsection
