@php
    $given = $answer->payload['value'] ?? '—';
    $expected = $question->config['expected'] ?? '—';
    $tolerance = (float) ($question->config['tolerance'] ?? 0);
@endphp

<div class="text-sm space-y-1">
    <div>Your answer: <span class="font-medium">{{ $given }}</span></div>
    <div class="text-emerald-700">
        Correct: <span class="font-medium">{{ $expected }}</span>
        @if ($tolerance > 0) <span class="text-slate-500">(± {{ $tolerance }})</span> @endif
    </div>
</div>
