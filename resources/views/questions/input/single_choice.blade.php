<div class="space-y-2">
    @foreach ($question->options as $option)
        <label class="flex items-start gap-3 rounded-md border border-slate-200 px-3 py-2 hover:bg-slate-50 cursor-pointer">
            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="mt-1">
            <span class="flex-1">
                @if ($option->image_path)
                    <img src="{{ Storage::url($option->image_path) }}" class="max-h-24 mb-1 rounded">
                @endif
                <span>{{ $option->label }}</span>
            </span>
        </label>
    @endforeach
</div>
