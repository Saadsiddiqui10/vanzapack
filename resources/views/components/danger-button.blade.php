<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn bg-rose-600 text-white hover:bg-rose-700 uppercase tracking-wide']) }}>
    {{ $slot }}
</button>
