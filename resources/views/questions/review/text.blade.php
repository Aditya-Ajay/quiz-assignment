@php
    $given = $answer->payload['value'] ?? '—';
    $expected = $question->config['expected'] ?? '—';
    $mode = $question->config['match'] ?? 'ci';
@endphp

<div class="text-sm space-y-1">
    <div>Your answer: <span class="font-medium">{{ $given !== '' ? $given : '—' }}</span></div>
    <div class="text-emerald-700">
        Expected: <span class="font-medium">{{ $expected }}</span>
        <span class="text-slate-500">({{ $mode }})</span>
    </div>
</div>
