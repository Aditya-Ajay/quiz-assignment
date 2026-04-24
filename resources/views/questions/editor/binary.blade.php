@php $correct = old('binary_correct', $question?->config['correct'] ?? 'yes'); @endphp

<div class="border-t border-slate-100 pt-4">
    <label class="block text-sm font-medium text-slate-700 mb-2">Correct answer</label>
    <div class="flex items-center gap-6">
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="radio" name="binary_correct" value="yes" @checked($correct === 'yes')> Yes / True
        </label>
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="radio" name="binary_correct" value="no" @checked($correct === 'no')> No / False
        </label>
    </div>
</div>
