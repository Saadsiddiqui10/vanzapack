<x-mail::message>
# Thank you, {{ $contact->name }}!

We've received your message and our team will get back to you shortly (Monday – Saturday, 9:00 AM – 6:00 PM).

<x-mail::panel>
{!! nl2br(e(\Illuminate\Support\Str::limit($contact->message, 600))) !!}
</x-mail::panel>

Need a faster answer? Chat with us on WhatsApp: +{{ settings('whatsapp_country_code', '971') }} {{ settings('whatsapp_phone') }}

<x-mail::button :url="route('shop.index')">
Browse our products
</x-mail::button>

Best regards,<br>
The {{ settings('store_name', 'VanzaPack') }} team
</x-mail::message>
