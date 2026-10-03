@props(['rating' => 0, 'count' => null, 'size' => 'sm'])

@php
    $rating = (float) $rating;
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5;
    $dim = $size === 'lg' ? 'h-5 w-5' : 'h-4 w-4';
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1']) }} aria-label="Rated {{ number_format($rating, 1) }} out of 5">
    <div class="flex text-amber-400">
        @for($i = 1; $i <= 5; $i++)
            <svg class="{{ $dim }}" viewBox="0 0 20 20" fill="{{ $i <= $full || ($i === $full + 1 && $half) ? 'currentColor' : 'none' }}" stroke="currentColor">
                <path stroke-width="1.5" d="M10 1.5l2.6 5.3 5.9.9-4.2 4.1 1 5.8L10 15l-5.3 2.8 1-5.8L1.5 7.7l5.9-.9L10 1.5z"/>
            </svg>
        @endfor
    </div>
    @if($count !== null)
        <span class="text-xs text-slate-400">({{ $count }})</span>
    @endif
</div>
