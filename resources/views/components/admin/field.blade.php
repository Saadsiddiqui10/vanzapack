@props(['label' => null, 'name' => null, 'hint' => null, 'required' => false])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label class="label" @if($name) for="{{ $name }}" @endif>
            {{ $label }}@if($required)<span class="ml-0.5 text-rose-500" title="Required">*</span>@endif
        </label>
    @endif
    {{ $slot }}
    @if($hint)<p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>@endif
    @if($name)<x-input-error :messages="$errors->get($name)" class="mt-1" />@endif
</div>
