<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index()
    {
        return view('admin.banners.index', [
            'banners' => Banner::orderBy('placement')->orderBy('position')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.banners.form', ['banner' => new Banner(['placement' => 'hero', 'is_active' => true])]);
    }

    public function store(Request $request)
    {
        Banner::create($this->data($request));

        return redirect()->route('admin.banners.index')->with('success', 'Banner created.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.form', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $banner->update($this->data($request, $banner));

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();

        return back()->with('success', 'Banner deleted.');
    }

    private function data(Request $request, ?Banner $banner = null): array
    {
        $data = $request->validate([
            'placement' => ['required', 'in:hero,promo,category'],
            'title' => ['nullable', 'string', 'max:150'],
            'subtitle' => ['nullable', 'string', 'max:250'],
            'cta_label' => ['nullable', 'string', 'max:50'],
            'cta_url' => ['nullable', 'string', 'max:250'],
            'is_active' => ['boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'image' => [$banner ? 'nullable' : 'required', 'image', 'max:8192'],
            'mobile_image' => ['nullable', 'image', 'max:8192'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['position'] = $data['position'] ?? 0;

        foreach (['image', 'mobile_image'] as $field) {
            if ($request->hasFile($field)) {
                if ($banner?->{$field} && ! Str::startsWith($banner->{$field}, 'http')) {
                    Storage::disk('public')->delete($banner->{$field});
                }
                $data[$field] = $request->file($field)->store('banners', 'public');
            } else {
                unset($data[$field]);
            }
        }

        return $data;
    }
}
