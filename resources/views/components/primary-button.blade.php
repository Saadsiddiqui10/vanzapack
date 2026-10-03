<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary w-full justify-center uppercase tracking-wide']) }}>
    {{ $slot }}
</button>
