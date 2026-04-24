@php
    $expected = old('expected', $question?->config['expected'] ?? '');
    $match = old('match', $question?->config['match'] ?? 'ci');
@endphp

<div class="border-t border-slate-100 pt-4 space-y-3">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Expected answer</label>
        <input type="text" name="expected" value="{{ $expected }}"
               class="w-full rounded-md border border-slate-300 px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Match mode</label>
        <div class="grid grid-cols-2 gap-2 text-sm">
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="match" value="exact" @checked($match === 'exact')> Exact (case-sensitive)
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="match" value="ci" @checked($match === 'ci')> Case-insensitive
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="match" value="contains" @checked($match === 'contains')> Contains
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="match" value="regex" @checked($match === 'regex')> Regex (use /.../i)
            </label>
        </div>
    </div>
</div>
