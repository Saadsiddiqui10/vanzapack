<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index()
    {
        return view('admin.pages.index', ['pages' => Page::orderBy('title')->paginate(30)]);
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => new Page(['is_published' => true])]);
    }

    public function store(Request $request)
    {
        Page::create($this->data($request));

        return redirect()->route('admin.pages.index')->with('success', 'Page created.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $page->update($this->data($request, $page));

        return redirect()->route('admin.pages.index')->with('success', 'Page updated.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return back()->with('success', 'Page deleted.');
    }

    private function data(Request $request, ?Page $page = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:170', Rule::unique('pages', 'slug')->ignore($page)],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:400'],
            'is_published' => ['boolean'],
            'featured_image' => ['nullable', 'image', 'max:8192'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['slug'] = ($data['slug'] ?? null) ?: Str::slug($data['title']);

        if ($request->hasFile('featured_image')) {
            if ($page?->featured_image) {
                Storage::disk('public')->delete($page->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('pages', 'public');
        } else {
            unset($data['featured_image']);
        }

        return $data;
    }
}
