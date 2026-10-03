<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index()
    {
        return view('admin.messages.index', [
            'messages' => ContactMessage::latest()->paginate(25),
        ]);
    }

    public function update(Request $request, ContactMessage $message)
    {
        $message->update(['is_read' => ! $message->is_read]);

        return back();
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return back()->with('success', 'Message deleted.');
    }
}
