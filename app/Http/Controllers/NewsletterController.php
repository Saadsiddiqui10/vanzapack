<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterWelcome;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $message = 'You are subscribed — watch your inbox for offers and packaging tips.';

        // Honeypot for bots (field is hidden from real visitors).
        if (filled($request->input('website'))) {
            return $this->respond($request, $message);
        }

        $data = $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ]);

        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => strtolower($data['email'])],
            ['is_confirmed' => true, 'unsubscribed_at' => null],
        );

        // Welcome email only for new sign-ups; a mail problem must not break the form.
        if ($subscriber->wasRecentlyCreated) {
            try {
                Mail::to($subscriber->email)->send(new NewsletterWelcome);
            } catch (Throwable $e) {
                report($e);
            }
        }

        return $this->respond($request, $message);
    }

    private function respond(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
