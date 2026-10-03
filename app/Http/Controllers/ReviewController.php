<?php

namespace App\Http\Controllers;

use App\Enums\ReviewStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function mine(Request $request)
    {
        $reviews = $request->user()->reviews()->with('product.images')->latest()->paginate(10);

        $pending = OrderItem::with('product.images', 'order')
            ->where('is_reviewed', false)
            ->whereHas('order', fn ($q) => $q->where('user_id', $request->user()->id)
                ->whereIn('status', ['delivered', 'shipped']))
            ->latest()
            ->get();

        return view('account.reviews', compact('reviews', 'pending'));
    }

    public function store(Request $request, Order $order, OrderItem $item)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_unless($item->order_id === $order->id, 404);
        abort_if($item->is_reviewed, 422, 'You have already reviewed this item.');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'comment' => ['required', 'string', 'min:10', 'max:2000'],
            'images.*' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
        ]);

        $review = Review::create([
            'product_id' => $item->product_id,
            'user_id' => $request->user()->id,
            'order_item_id' => $item->id,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'comment' => $data['comment'],
            'status' => ReviewStatus::Pending->value,
            'is_verified_purchase' => true,
        ]);

        foreach ($request->file('images', []) as $image) {
            $review->images()->create([
                'path' => $image->store('reviews', 'public'),
            ]);
        }

        $item->update(['is_reviewed' => true]);

        return back()->with('success', 'Thanks! Your review is awaiting moderation.');
    }
}
