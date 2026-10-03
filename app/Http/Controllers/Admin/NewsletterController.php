<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{
    public function index()
    {
        return view('admin.newsletter.index', [
            'subscribers' => NewsletterSubscriber::latest()->paginate(50),
        ]);
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('success', 'Subscriber removed.');
    }
}
