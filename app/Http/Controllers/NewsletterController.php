<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ]);

        NewsletterSubscriber::updateOrCreate(
            ['email' => strtolower($data['email'])],
            ['is_confirmed' => true, 'unsubscribed_at' => null],
        );

        $message = 'You are subscribed — watch your inbox for offers and packaging tips.';

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
