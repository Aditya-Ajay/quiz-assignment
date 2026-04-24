@php
    $expected = old('expected', $question?->config['expected'] ?? '');
    $tolerance = old('tolerance', $question?->config['tolerance'] ?? 0);
@endphp

<div class="border-t border-slate-100 pt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Expected answer</label>
        <input type="number" step="any" name="expected" value="{{ $expected }}"
               class="w-full rounded-md border border-slate-300 px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Tolerance (±)</label>
        <input type="number" step="any" name="tolerance" value="{{ $tolerance }}"
               class="w-full rounded-md border border-slate-300 px-3 py-2">
        <p class="text-xs text-slate-500 mt-1">Answers within ± this amount count as correct.</p>
    </div>
</div>
