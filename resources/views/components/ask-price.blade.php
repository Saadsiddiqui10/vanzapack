@props(['product'])

{{-- Catalog mode: "Ask for price" opens WhatsApp with the product pre-filled (no URL shown on hover). --}}
<button type="button" data-wa-href="{{ app(\App\Services\WhatsAppService::class)->priceEnquiryLink($product) }}"
        {{ $attributes->merge(['class' => 'btn inline-flex w-full items-center justify-center gap-1.5 bg-[#25D366] text-white hover:bg-[#1ebe5b]']) }}>
    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.7-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.7 1-.9 1.1-.2.2-.3.2-.6.1-1.7-.8-2.8-1.5-3.9-3.4-.3-.5.3-.5.8-1.5.1-.2 0-.4 0-.5 0-.1-.7-1.6-.9-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.3 5.2 4.6 2.9 1.2 2.9.8 3.4.8.5 0 1.7-.7 1.9-1.4.2-.7.2-1.2.2-1.4-.1-.1-.3-.2-.6-.3M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2"/></svg>
    <span class="whitespace-nowrap">Ask for price</span>
</button>
