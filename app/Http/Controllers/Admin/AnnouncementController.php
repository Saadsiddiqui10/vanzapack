<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('admin.announcements.index', [
            'announcements' => Announcement::orderBy('position')->orderBy('id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.announcements.form', ['announcement' => new Announcement(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        Announcement::create($this->data($request));

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement added.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.form', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $announcement->update($this->data($request));

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Announcement removed.');
    }

    private function data(Request $request): array
    {
        $data = $request->validate([
            'text' => ['required', 'string', 'max:200'],
            'url' => ['nullable', 'string', 'max:250'],
            'is_active' => ['boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['position'] = $data['position'] ?? 0;

        return $data;
    }
}
