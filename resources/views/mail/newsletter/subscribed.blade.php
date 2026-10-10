<x-mail::message>
# New newsletter subscriber

**{{ $subscriber->email }}** just joined the {{ settings('store_name', 'VanzaPack') }} newsletter.

You now have **{{ $total }}** {{ \Illuminate\Support\Str::plural('subscriber', $total) }}.

<x-mail::button :url="route('admin.newsletter.index')">
View subscribers
</x-mail::button>
</x-mail::message>
