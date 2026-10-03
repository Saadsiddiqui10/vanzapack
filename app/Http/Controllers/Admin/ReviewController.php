<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReviewStatus;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::query()
            ->with(['product', 'user', 'images'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function update(Request $request, Review $review)
    {
        $data = $request->validate(['status' => ['required', new Enum(ReviewStatus::class)]]);
        $review->update($data);

        return back()->with('success', 'Review '.$review->status->label().'.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
