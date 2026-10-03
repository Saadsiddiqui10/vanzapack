<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-outline uppercase tracking-wide']) }}>
    {{ $slot }}
</button>
