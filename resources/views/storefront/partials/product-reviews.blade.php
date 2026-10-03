<div class="grid gap-8 md:grid-cols-[16rem_1fr]">
    <div>
        <p class="text-4xl font-bold text-brand-800">{{ number_format($product->rating_avg, 1) }}</p>
        <x-rating-stars :rating="$product->rating_avg" size="lg" />
        <p class="mt-1 text-sm text-slate-400">{{ $product->rating_count }} review{{ $product->rating_count === 1 ? '' : 's' }}</p>

        <div class="mt-4 space-y-1">
            @foreach($ratingBreakdown as $star => $count)
                <div class="flex items-center gap-2 text-xs">
                    <span class="w-8 text-slate-500">{{ $star }} ★</span>
                    <div class="h-2 flex-1 overflow-hidden rounded bg-slate-100">
                        <div class="h-full bg-amber-400" style="width: {{ $product->rating_count ? ($count / $product->rating_count * 100) : 0 }}%"></div>
                    </div>
                    <span class="w-6 text-right text-slate-400">{{ $count }}</span>
                </div>
            @endforeach
        </div>

        @auth
            @if($canReview)
                <p class="mt-4 text-sm text-slate-500">You've purchased this product — <a href="{{ route('account.reviews') }}" class="link">write a review</a>.</p>
            @endif
        @else
            <p class="mt-4 text-sm text-slate-500"><a href="{{ route('login') }}" class="link">Sign in</a> to review products you've bought.</p>
        @endauth
    </div>

    <div class="space-y-5">
        @forelse($product->approvedReviews as $review)
            <article class="border-b border-slate-100 pb-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-800">
                            {{ $review->user->initials() }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-brand-800">{{ $review->user->name }}</p>
                            <div class="flex items-center gap-2">
                                <x-rating-stars :rating="$review->rating" />
                                @if($review->is_verified_purchase)
                                    <span class="badge bg-brand-50 text-brand-700">Verified purchase</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <time class="text-xs text-slate-400">{{ $review->created_at->diffForHumans() }}</time>
                </div>
                @if($review->title)<p class="mt-2 text-sm font-semibold text-brand-800">{{ $review->title }}</p>@endif
                <p class="mt-1 text-sm text-slate-600">{{ $review->comment }}</p>
                @if($review->images->isNotEmpty())
                    <div class="mt-2 flex gap-2">
                        @foreach($review->images as $img)
                            <img src="{{ $img->url() }}" alt="" class="h-16 w-16 rounded object-cover">
                        @endforeach
                    </div>
                @endif
            </article>
        @empty
            <p class="text-sm text-slate-400">No reviews yet — be the first to review this product.</p>
        @endforelse
    </div>
</div>
