@props([
    'title' => '',
    'first' => 'text-brand-500',   // primary colour word(s)
    'rest' => 'text-navy-600',      // secondary colour word(s)
])

@php
    // strip a leading emoji/symbol so colouring lands on the actual words
    $clean = trim(preg_replace('/^\p{So}\p{Sk}*\s*/u', '', $title));
    $lead = mb_substr($title, 0, mb_strlen($title) - mb_strlen($clean));
    $parts = explode(' ', $clean, 2);
@endphp

<h2 {{ $attributes->merge(['class' => 'font-display text-2xl font-bold']) }}>
    @if($lead){{ $lead }}@endif<span class="{{ $first }}">{{ $parts[0] }}</span>@isset($parts[1]) <span class="{{ $rest }}">{{ $parts[1] }}</span>@endisset
</h2>
