<x-account-layout title="Reviews">
    @if($pending->isNotEmpty())
        <div class="card mb-6 p-5">
            <h2 class="mb-3 font-semibold text-brand-800">Awaiting your review</h2>
            <div class="space-y-4">
                @foreach($pending as $item)
                    <div class="border-b border-slate-100 pb-4 last:border-0" x-data="{ open: false }">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->product?->primaryImageUrl() ?? 'https://placehold.co/64' }}" alt="" class="h-12 w-12 rounded object-cover">
                            <div class="flex-1 text-sm">
                                <p class="font-medium text-brand-800">{{ $item->name }}</p>
                                <p class="text-xs text-slate-400">Order {{ $item->order->number }}</p>
                            </div>
                            <button class="btn-outline py-1.5 text-xs" @click="open = !open">Write review</button>
                        </div>
                        <form x-show="open" x-cloak method="POST" enctype="multipart/form-data"
                              action="{{ route('account.reviews.store', [$item->order->number, $item->id]) }}"
                              class="mt-3 space-y-3 text-sm">
                            @csrf
                            <div>
                                <label class="label">Rating</label>
                                <select name="rating" class="field w-32">
                                    @for($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>@endfor
                                </select>
                            </div>
                            <div><label class="label">Title</label><input name="title" class="field"></div>
                            <div><label class="label">Your review</label><textarea name="comment" rows="3" required class="field"></textarea></div>
                            <div><label class="label">Photos (optional)</label><input type="file" name="images[]" multiple accept="image/*" class="text-xs"></div>
                            <button class="btn-primary">Submit review</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="card p-5">
        <h2 class="mb-3 font-semibold text-brand-800">Your reviews</h2>
        @forelse($reviews as $review)
            <div class="border-b border-slate-100 py-3 last:border-0">
                <div class="flex items-center justify-between">
                    <a href="{{ $review->product->url() }}" class="text-sm font-medium text-brand-800">{{ $review->product->name }}</a>
                    <span class="badge {{ $review->status->value === 'approved' ? 'bg-brand-100 text-brand-800' : ($review->status->value === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800') }}">
                        {{ $review->status->label() }}
                    </span>
                </div>
                <x-rating-stars :rating="$review->rating" class="mt-1" />
                <p class="mt-1 text-sm text-slate-500">{{ $review->comment }}</p>
            </div>
        @empty
            <p class="text-sm text-slate-400">You haven't written any reviews yet.</p>
        @endforelse
        <div class="mt-4">{{ $reviews->links() }}</div>
    </div>
</x-account-layout>
