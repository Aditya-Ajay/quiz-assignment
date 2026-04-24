@php
    $options = $question?->options ?? collect();
    $rows = old('options', $options->pluck('label')->all() ?: ['', '']);
    $correctIndex = (int) old('correct_option', $options->search(fn ($o) => $o->is_correct) ?: 0);
@endphp

<div class="border-t border-slate-100 pt-4">
    <label class="block text-sm font-medium text-slate-700 mb-2">Options (pick one correct)</label>
    <div class="space-y-2" data-options-root>
        @foreach ($rows as $i => $row)
            <div class="flex items-center gap-3" data-option-row>
                <input type="radio" name="correct_option" value="{{ $i }}" @checked($i === $correctIndex)>
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
    <button type="button" onclick="addOption(this)" class="mt-2 text-sm text-slate-600 hover:text-slate-900">+ Add option</button>
</div>

<script>
    function addOption(btn) {
        const root = btn.previousElementSibling;
        const rows = root.querySelectorAll('[data-option-row]');
        const i = rows.length;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3';
        row.setAttribute('data-option-row', '');
        row.innerHTML = `
            <input type="radio" name="correct_option" value="${i}">
            <input type="text" name="options[]" placeholder="Option text" class="flex-1 rounded-md border border-slate-300 px-3 py-2">
            <input type="file" name="option_images[${i}]" accept="image/*" class="text-xs">
        `;
        root.appendChild(row);
    }
</script>
