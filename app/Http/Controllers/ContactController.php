<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Page;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        $page = Page::published()->where('slug', 'contact')->first();

        return view('storefront.contact', compact('page'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        ContactMessage::create($data);

        return back()->with('success', 'Thank you — our team will get back to you shortly.');
    }
}
