@php
    $given = $answer->payload['value'] ?? '—';
    $expected = $question->config['correct'] ?? 'yes';
@endphp

<div class="text-sm space-y-1">
    <div>Your answer: <span class="font-medium">{{ strtoupper($given) }}</span></div>
    @if ($given !== $expected)
        <div class="text-emerald-700">Correct: <span class="font-medium">{{ strtoupper($expected) }}</span></div>
    @endif
</div>
