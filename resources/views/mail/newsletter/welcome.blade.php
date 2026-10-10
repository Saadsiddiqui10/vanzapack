<x-mail::message>
# You're subscribed!

Thanks for joining the {{ settings('store_name', 'VanzaPack') }} newsletter. You'll be the first to hear about new products, special offers and packaging tips.

<x-mail::button :url="route('shop.offers')">
See today's deals
</x-mail::button>

Best regards,<br>
The {{ settings('store_name', 'VanzaPack') }} team
</x-mail::message>
