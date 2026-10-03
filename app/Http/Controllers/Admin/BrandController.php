<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index()
    {
        return view('admin.brands.index', [
            'brands' => Brand::withCount('products')->orderBy('name')->paginate(30),
        ]);
    }

    public function create()
    {
        return view('admin.brands.form', ['brand' => new Brand(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        $brand = Brand::create($this->data($request));
        ActivityLogger::log('brand.created', "Created brand {$brand->name}", $brand);

        return redirect()->route('admin.brands.index')->with('success', 'Brand created.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.form', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $brand->update($this->data($request, $brand));
        ActivityLogger::log('brand.updated', "Updated brand {$brand->name}", $brand);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated.');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return back()->with('success', 'Brand deleted.');
    }

    private function data(Request $request, ?Brand $brand = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:170', Rule::unique('brands', 'slug')->ignore($brand)],
            'description' => ['nullable', 'string', 'max:2000'],
            'website' => ['nullable', 'url', 'max:200'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:400'],
            'logo' => ['nullable', 'image', 'max:8192'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['position'] = $data['position'] ?? 0;
        $data['slug'] = ($data['slug'] ?? null) ?: Str::slug($data['name']);

        if ($request->hasFile('logo')) {
            if ($brand?->logo && ! Str::startsWith($brand->logo, 'http')) {
                Storage::disk('public')->delete($brand->logo);
            }
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        } else {
            unset($data['logo']);
        }

        return $data;
    }
}
