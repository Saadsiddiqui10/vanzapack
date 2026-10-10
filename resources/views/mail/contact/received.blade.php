<x-mail::message>
# New website enquiry

**Name:** {{ $contact->name }}<br>
**Email:** {{ $contact->email }}<br>
@if($contact->phone)
**Phone:** {{ $contact->phone }}<br>
@endif
@if($contact->subject)
**Subject:** {{ $contact->subject }}
@endif

<x-mail::panel>
{!! nl2br(e($contact->message)) !!}
</x-mail::panel>

Reply to this email to answer {{ $contact->name }} directly.

<x-mail::button :url="route('admin.messages.index')">
Open in admin
</x-mail::button>
</x-mail::message>
