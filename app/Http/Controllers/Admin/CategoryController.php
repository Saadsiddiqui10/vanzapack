<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $trashed = $request->boolean('trashed');

        return view('admin.categories.index', [
            'categories' => Category::query()
                ->when($trashed, fn ($q) => $q->onlyTrashed())
                ->with('parent')->withCount('products')
                ->orderBy('position')->orderBy('name')
                ->paginate(30)
                ->withQueryString(),
            'trashed' => $trashed,
            'archivedCount' => Category::onlyTrashed()->count(),
        ]);
    }

    public function create()
    {
        return view('admin.categories.form', [
            'category' => new Category(['is_active' => true]),
            'parents' => Category::whereNull('parent_id')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $category = Category::create($this->data($request));
        ActivityLogger::log('category.created', "Created category {$category->name}", $category);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', [
            'category' => $category,
            'parents' => Category::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->data($request, $category));
        ActivityLogger::log('category.updated', "Updated category {$category->name}", $category);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        abort_if($category->products()->exists(), 422, 'Move or delete this category\'s products first.');
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:categories,id'],
        ]);

        $categories = Category::whereIn('id', $data['ids'])->withCount('products')->get();

        $deleted = 0;
        $skipped = 0;
        foreach ($categories as $category) {
            if ($category->products_count > 0) {
                $skipped++;

                continue;
            }
            $category->delete();
            $deleted++;
        }

        ActivityLogger::log('category.bulk_deleted', "Deleted {$deleted} category(ies)");

        $message = "{$deleted} category(ies) deleted.";
        if ($skipped > 0) {
            $message .= " {$skipped} skipped because they still contain products.";
        }

        return redirect()->route('admin.categories.index')
            ->with($deleted > 0 ? 'success' : 'error', $message);
    }

    // ── Archive (soft-delete) management ────────────────────────────
    public function restore(string $id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();
        ActivityLogger::log('category.restored', "Restored category {$category->name}", $category);

        return redirect()->route('admin.categories.index', ['trashed' => 1])
            ->with('success', "\"{$category->name}\" restored.");
    }

    public function forceDestroy(string $id)
    {
        $category = Category::onlyTrashed()
            ->withCount(['products' => fn ($q) => $q->withTrashed()])
            ->findOrFail($id);

        if ($category->products_count > 0) {
            return redirect()->route('admin.categories.index', ['trashed' => 1])
                ->with('error', "\"{$category->name}\" still has {$category->products_count} product(s) (including archived ones). Permanently delete those products first.");
        }

        $name = $category->name;
        Category::withTrashed()->where('parent_id', $category->id)->update(['parent_id' => null]);
        $category->forceDelete();
        ActivityLogger::log('category.force_deleted', "Permanently deleted category {$name}");

        return redirect()->route('admin.categories.index', ['trashed' => 1])
            ->with('success', "\"{$name}\" permanently deleted.");
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer']])['ids'];

        $count = Category::onlyTrashed()->whereIn('id', $ids)->get()->each->restore()->count();
        ActivityLogger::log('category.bulk_restored', "Restored {$count} category(ies)");

        return redirect()->route('admin.categories.index', ['trashed' => 1])
            ->with('success', "{$count} category(ies) restored.");
    }

    public function bulkForceDestroy(Request $request)
    {
        $ids = $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer']])['ids'];

        $categories = Category::onlyTrashed()
            ->withCount(['products' => fn ($q) => $q->withTrashed()])
            ->whereIn('id', $ids)->get();

        $deleted = 0;
        $skipped = 0;
        foreach ($categories as $category) {
            if ($category->products_count > 0) {
                $skipped++;

                continue;
            }

            try {
                Category::withTrashed()->where('parent_id', $category->id)->update(['parent_id' => null]);
                $category->forceDelete();
                $deleted++;
            } catch (\Illuminate\Database\QueryException $e) {
                report($e);
                $skipped++;
            }
        }

        ActivityLogger::log('category.bulk_force_deleted', "Permanently deleted {$deleted} category(ies)");

        $message = "{$deleted} category(ies) permanently deleted.";
        if ($skipped > 0) {
            $message .= " {$skipped} skipped — they still have products (archive tab included). Permanently delete those products first.";
        }

        return redirect()->route('admin.categories.index', ['trashed' => 1])
            ->with($deleted > 0 ? 'success' : 'error', $message);
    }

    private function data(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:170', Rule::unique('categories', 'slug')->ignore($category)],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:400'],
            'image' => ['nullable', 'image', 'max:8192'],
            'banner' => ['nullable', 'image', 'max:8192'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['slug'] = ($data['slug'] ?? null) ?: Str::slug($data['name']);
        $data['position'] = $data['position'] ?? 0;

        foreach (['image', 'banner'] as $field) {
            if ($request->hasFile($field)) {
                if ($category?->{$field} && ! Str::startsWith($category->{$field}, 'http')) {
                    Storage::disk('public')->delete($category->{$field});
                }
                $data[$field] = $request->file($field)->store('categories', 'public');
            } else {
                unset($data[$field]);
            }
        }

        return $data;
    }
}
