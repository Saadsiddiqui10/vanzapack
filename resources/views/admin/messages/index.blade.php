<x-admin-layout title="Messages" active="messages">
    <x-admin.head title="Contact messages" />
    <div class="space-y-3">
        @forelse($messages as $message)
            <div class="rounded-xl border border-slate-200 bg-white p-4 {{ $message->is_read ? '' : 'border-l-4 border-l-brand-500' }}">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <span class="font-medium text-brand-800">{{ $message->name }}</span>
                        <span class="text-sm text-slate-400">· {{ $message->email }}{{ $message->phone ? ' · '.$message->phone : '' }}</span>
                    </div>
                    <span class="text-xs text-slate-400">{{ $message->created_at->format('d M Y H:i') }}</span>
                </div>
                @if($message->subject)<p class="mt-1 text-sm font-semibold text-brand-800">{{ $message->subject }}</p>@endif
                <p class="mt-1 whitespace-pre-wrap text-sm text-slate-600">{{ $message->message }}</p>
                <div class="mt-2 flex gap-3 text-xs">
                    <form method="POST" action="{{ route('admin.messages.update', $message) }}">@csrf @method('PUT')<button class="text-brand-600 hover:underline">Mark as {{ $message->is_read ? 'unread' : 'read' }}</button></form>
                    <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('Delete message?')">@csrf @method('DELETE')<button class="text-rose-500 hover:underline">Delete</button></form>
                </div>
            </div>
        @empty
            <p class="rounded-xl border border-slate-200 bg-white p-6 text-center text-slate-400">No messages yet.</p>
        @endforelse
    </div>
    <div class="mt-4">{{ $messages->links() }}</div>
</x-admin-layout>
