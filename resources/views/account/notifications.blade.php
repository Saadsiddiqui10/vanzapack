<x-account-layout title="Notifications">
    <form method="POST" action="{{ route('account.notifications.read') }}" class="mb-4">
        @csrf
        <button class="btn-outline py-2 text-sm">Mark all as read</button>
    </form>

    <div class="card divide-y divide-slate-100">
        @forelse($notifications as $notification)
            <a href="{{ $notification->data['url'] ?? '#' }}"
               class="block p-4 text-sm {{ $notification->read_at ? '' : 'bg-brand-50/50' }}">
                <p class="text-brand-800">{{ $notification->data['message'] ?? 'Notification' }}</p>
                <p class="text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
            </a>
        @empty
            <p class="p-6 text-center text-sm text-slate-400">No notifications.</p>
        @endforelse
    </div>
    <div class="mt-4">{{ $notifications->links() }}</div>
</x-account-layout>
