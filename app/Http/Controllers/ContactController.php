<?php

namespace App\Http\Controllers;

use App\Mail\ContactAutoReply;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Support\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function show()
    {
        $page = Page::published()->where('slug', 'contact')->first();

        return view('storefront.contact', compact('page'));
    }

    public function store(Request $request)
    {
        $success = 'Thank you — our team will get back to you shortly.';

        // Honeypot: real visitors never see or fill this field; bots do.
        if (filled($request->input('website'))) {
            return back()->with('success', $success);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $contact = ContactMessage::create($data);

        // The message is already saved in Admin → Messages, so a mail problem
        // (e.g. SMTP not configured) must never show the visitor an error.
        try {
            if ($inbox = Notify::inbox()) {
                Mail::to($inbox)->send(new ContactMessageReceived($contact));
            }
            Mail::to($contact->email, $contact->name)->send(new ContactAutoReply($contact));
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('success', $success);
    }
}
