<x-admin-layout title="Reviews" active="reviews">
    <x-admin.head title="Product reviews" />

    <div class="mb-4 flex gap-2 text-sm">
        @foreach(['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $val => $label)
            <a href="{{ route('admin.reviews.index', array_filter(['status' => $val])) }}"
               class="rounded-lg px-3 py-1.5 {{ request('status', '') === $val ? 'bg-brand-700 text-white' : 'bg-white border border-slate-200' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="space-y-3">
        @foreach($reviews as $review)
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <a href="{{ $review->product->url() }}" target="_blank" class="font-medium text-brand-800 hover:underline">{{ $review->product->name }}</a>
                        <span class="text-sm text-slate-400">· {{ $review->user->name }} · {{ $review->created_at->format('d M Y') }}</span>
                    </div>
                    <x-rating-stars :rating="$review->rating" />
                </div>
                @if($review->title)<p class="mt-1 text-sm font-semibold text-brand-800">{{ $review->title }}</p>@endif
                <p class="mt-1 text-sm text-slate-600">{{ $review->comment }}</p>
                @if($review->images->isNotEmpty())
                    <div class="mt-2 flex gap-2">@foreach($review->images as $img)<img src="{{ $img->url() }}" class="h-14 w-14 rounded object-cover">@endforeach</div>
                @endif
                <div class="mt-3 flex items-center gap-2">
                    <span class="badge {{ $review->status->value === 'approved' ? 'bg-brand-100 text-brand-800' : ($review->status->value === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800') }}">{{ $review->status->label() }}</span>
                    @foreach(['approved' => 'Approve', 'rejected' => 'Reject', 'pending' => 'Reset'] as $val => $label)
                        <form method="POST" action="{{ route('admin.reviews.update', $review) }}">@csrf @method('PUT')<input type="hidden" name="status" value="{{ $val }}"><button class="text-xs text-brand-600 hover:underline">{{ $label }}</button></form>
                    @endforeach
                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete review?')">@csrf @method('DELETE')<button class="text-xs text-rose-500 hover:underline">Delete</button></form>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $reviews->links() }}</div>
</x-admin-layout>
