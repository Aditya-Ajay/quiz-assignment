@php $q = $question ?? null; @endphp

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Question (HTML allowed)</label>
    <textarea name="body_html" rows="3" required
              class="w-full rounded-md border border-slate-300 px-3 py-2 font-mono text-sm">{{ old('body_html', $q?->body_html) }}</textarea>
    <p class="text-xs text-slate-500 mt-1">Use plain text or HTML: <code>&lt;b&gt;</code>, <code>&lt;i&gt;</code>, <code>&lt;br&gt;</code>, etc.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Image (optional)</label>
        @if ($q?->image_path)
            <div class="mb-2 flex items-center gap-2">
                <img src="{{ Storage::url($q->image_path) }}" class="h-16 rounded border border-slate-200">
                <label class="text-xs text-slate-600">
                    <input type="checkbox" name="remove_image" value="1"> Remove
                </label>
            </div>
        @endif
        <input type="file" name="image" accept="image/*" class="text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Video URL (YouTube)</label>
        <input type="url" name="video_url" value="{{ old('video_url', $q?->video_url) }}"
               placeholder="https://youtu.be/..."
               class="w-full rounded-md border border-slate-300 px-3 py-2">
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Marks</label>
    <input type="number" name="marks" min="1" value="{{ old('marks', $q?->marks ?? 1) }}" required
           class="w-32 rounded-md border border-slate-300 px-3 py-2">
</div>
