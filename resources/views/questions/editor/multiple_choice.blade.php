@php
    $options = $question?->options ?? collect();
    $rows = old('options', $options->pluck('label')->all() ?: ['', '']);
    $correctIndexes = old('correct_options', $options->filter->is_correct->keys()->all());
    $scoring = old('scoring', $question?->config['scoring'] ?? 'strict');
@endphp

<div class="border-t border-slate-100 pt-4 space-y-3">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Options (tick all correct)</label>
        <div class="space-y-2" data-mc-root>
            @foreach ($rows as $i => $row)
                <div class="flex items-center gap-3" data-mc-row>
                    <input type="checkbox" name="correct_options[]" value="{{ $i }}" @checked(in_array($i, $correctIndexes))>
                    <input type="text" name="options[]" value="{{ $row }}"
                           placeholder="Option text"
                           class="flex-1 rounded-md border border-slate-300 px-3 py-2">
                    <input type="file" name="option_images[{{ $i }}]" accept="image/*" class="text-xs">
                    @if ($options->get($i)?->image_path)
                        <img src="{{ Storage::url($options[$i]->image_path) }}" class="h-10 w-10 object-cover rounded">
                    @endif
                </div>
            @endforeach
        </div>
        <button type="button" onclick="addMcOption(this)" class="mt-2 text-sm text-slate-600 hover:text-slate-900">+ Add option</button>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Scoring</label>
        <label class="inline-flex items-center gap-2 text-sm mr-4">
            <input type="radio" name="scoring" value="strict" @checked($scoring === 'strict')> All-or-nothing
        </label>
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="radio" name="scoring" value="partial" @checked($scoring === 'partial')> Partial credit
        </label>
    </div>
</div>

<script>
    function addMcOption(btn) {
        const root = btn.previousElementSibling;
        const rows = root.querySelectorAll('[data-mc-row]');
        const i = rows.length;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3';
        row.setAttribute('data-mc-row', '');
        row.innerHTML = `
            <input type="checkbox" name="correct_options[]" value="${i}">
            <input type="text" name="options[]" placeholder="Option text" class="flex-1 rounded-md border border-slate-300 px-3 py-2">
            <input type="file" name="option_images[${i}]" accept="image/*" class="text-xs">
        `;
        root.appendChild(row);
    }
</script>
