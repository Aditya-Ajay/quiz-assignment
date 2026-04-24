@php
    $pickedIds = $answer->payload['option_ids'] ?? [];
    $picked = $question->options->whereIn('id', $pickedIds)->pluck('label')->all();
    $correct = $question->options->where('is_correct', true)->pluck('label')->all();
@endphp

<div class="text-sm space-y-1">
    <div>Your answer: <span class="font-medium">{{ $picked ? implode(', ', $picked) : '—' }}</span></div>
    <div class="text-emerald-700">Correct: <span class="font-medium">{{ implode(', ', $correct) }}</span></div>
</div>
