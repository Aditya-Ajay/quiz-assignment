@php
    $pickedId = $answer->payload['option_id'] ?? null;
    $picked = $question->options->firstWhere('id', $pickedId);
    $correct = $question->options->firstWhere('is_correct', true);
@endphp

<div class="text-sm space-y-1">
    <div>Your answer: <span class="font-medium">{{ $picked?->label ?? '—' }}</span></div>
    @if (! $picked || ! $picked->is_correct)
        <div class="text-emerald-700">Correct: <span class="font-medium">{{ $correct?->label }}</span></div>
    @endif
</div>
