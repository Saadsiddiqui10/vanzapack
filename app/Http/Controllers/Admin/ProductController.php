<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\InventoryService;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $trashed = $request->boolean('trashed');

        $products = Product::query()
            ->when($trashed, fn ($q) => $q->onlyTrashed())
            ->with(['category', 'brand', 'inventory'])
            ->when($request->string('q')->toString(), fn ($q, $term) => $q->search($term))
            ->when($request->input('category'), fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest($trashed ? 'deleted_at' : 'created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'trashed' => $trashed,
            'archivedCount' => Product::onlyTrashed()->count(),
            'draftCount' => Product::where('status', 'draft')->count(),
            // stock records (a product with sizes/packs has one per variant)
            'outOfStockCount' => Inventory::whereHas('product')->whereRaw('quantity - reserved <= 0')->count(),
            'lowStockCount' => Inventory::whereHas('product')->whereRaw('quantity - reserved <= low_stock_threshold')->count(),
        ]);
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product(['status' => 'active', 'track_inventory' => true]),
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));

        $product = Product::create($data);
        $this->syncInventory($product, $request);
        $this->handleImageUploads($product, $request);

        ActivityLogger::log('product.created', "Created product {$product->name}", $product);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $product->load('images', 'inventory', 'variants');

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, $product);
        $product->update($data);
        $this->syncInventory($product, $request);
        $this->handleImageUploads($product, $request);

        ActivityLogger::log('product.updated', "Updated product {$product->name}", $product);

        return back()->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        ActivityLogger::log('product.deleted', "Deleted product {$product->name}", $product);

        return redirect()->route('admin.products.index')->with('success', 'Product moved to the bin.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $this->validatedIds($request);

        $count = Product::whereIn('id', $ids)->get()->each->delete()->count();
        ActivityLogger::log('product.bulk_deleted', "Moved {$count} product(s) to the bin");

        return redirect()->route('admin.products.index')
            ->with('success', "{$count} product(s) moved to the bin. They can be restored from the Bin tab.");
    }

    // ── Status (active = in the store; draft / archived = hidden) ───
    private const STATUS_LABELS = ['active' => 'active', 'draft' => 'draft', 'archived' => 'archived (hidden from the store)'];

    /** Set the status of the ticked products. */
    public function bulkStatus(Request $request)
    {
        $ids = $this->validatedIds($request);
        $status = $request->validate(['status' => ['required', Rule::in(array_keys(self::STATUS_LABELS))]])['status'];

        $count = $this->applyStatus(Product::whereIn('id', $ids), $status);
        ActivityLogger::log('product.bulk_status', "Set {$count} product(s) to {$status}");

        return back()->with('success', "{$count} product(s) set to ".self::STATUS_LABELS[$status].'.');
    }

    /** Make every draft product active, or archive every draft — not just the current page. */
    public function draftsStatus(Request $request)
    {
        $status = $request->validate(['status' => ['required', Rule::in(['active', 'archived'])]])['status'];

        $count = $this->applyStatus(Product::where('status', 'draft'), $status);
        ActivityLogger::log('product.drafts_status', "Set all {$count} draft product(s) to {$status}");

        return back()->with('success', "All {$count} draft product(s) set to ".self::STATUS_LABELS[$status].'.');
    }

    // ── Bulk stock ──────────────────────────────────────────────────
    /**
     * Update stock for ticked products, or for all / out-of-stock / low-stock products.
     * Products with variants update every variant's stock.
     */
    public function bulkStock(Request $request, InventoryService $inventory)
    {
        $data = $request->validate([
            'scope' => ['required', Rule::in(['selected', 'all', 'out_of_stock', 'low_stock'])],
            'mode' => ['required', Rule::in(['set', 'add', 'subtract'])],
            'quantity' => ['required', 'integer', 'min:0', 'max:1000000'],
            'ids' => ['required_if:scope,selected', 'array'],
            'ids.*' => ['integer'],
        ]);

        @set_time_limit(0);
        $qty = (int) $data['quantity'];
        $updated = 0;
        $products = 0;

        Product::query()
            ->when($data['scope'] === 'selected', fn ($q) => $q->whereIn('id', $data['ids']))
            ->with('variants')
            ->orderBy('id')
            ->chunk(100, function ($chunk) use ($inventory, $data, $qty, &$updated, &$products) {
                foreach ($chunk as $product) {
                    $touched = false;
                    $targets = $product->has_variants && $product->variants->isNotEmpty() ? $product->variants : [null];

                    foreach ($targets as $variant) {
                        $row = $inventory->for($product, $variant);

                        if ($data['scope'] === 'out_of_stock' && $row->available() > 0) {
                            continue;
                        }
                        if ($data['scope'] === 'low_stock' && ! ($row->isLow() || $row->available() <= 0)) {
                            continue;
                        }

                        $new = match ($data['mode']) {
                            'set' => $qty,
                            'add' => $row->quantity + $qty,
                            'subtract' => max(0, $row->quantity - $qty),
                        };
                        if ($new === (int) $row->quantity) {
                            continue;
                        }

                        $inventory->adjust($row, $new, 'Bulk stock update', notify: false);
                        $updated++;
                        $touched = true;
                    }

                    $products += $touched ? 1 : 0;
                }
            });

        $verb = ['set' => "set to {$qty}", 'add' => "increased by {$qty}", 'subtract' => "reduced by {$qty}"][$data['mode']];
        ActivityLogger::log('product.bulk_stock', "Bulk stock ({$data['scope']}): {$verb} on {$updated} stock record(s)");

        return back()->with('success', $updated
            ? "Stock {$verb} for {$products} product(s) ({$updated} stock record(s) including sizes/packs)."
            : 'No stock needed changing.');
    }

    private function applyStatus($query, string $status): int
    {
        // One model at a time so model events / cache clearing still run.
        return $query->get()->each(function (Product $product) use ($status) {
            $product->status = $status;
            if ($status === 'active' && ! $product->published_at) {
                $product->published_at = now(); // first time it goes live
            }
            $product->save();
        })->count();
    }

    // ── Archive (soft-delete) management ────────────────────────────
    public function restore(string $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        ActivityLogger::log('product.restored', "Restored product {$product->name}", $product);

        return redirect()->route('admin.products.index', ['trashed' => 1])
            ->with('success', "\"{$product->name}\" restored.");
    }

    public function forceDestroy(string $id)
    {
        $product = Product::onlyTrashed()->with('images')->findOrFail($id);
        $name = $product->name;

        try {
            $this->purge($product);
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);

            return redirect()->route('admin.products.index', ['trashed' => 1])
                ->with('error', "\"{$name}\" could not be permanently deleted because other records still reference it.");
        }

        ActivityLogger::log('product.force_deleted', "Permanently deleted product {$name}");

        return redirect()->route('admin.products.index', ['trashed' => 1])
            ->with('success', "\"{$name}\" permanently deleted.");
    }

    public function bulkRestore(Request $request)
    {
        $ids = $this->validatedIds($request, trashed: true);

        $count = Product::onlyTrashed()->whereIn('id', $ids)->get()->each->restore()->count();
        ActivityLogger::log('product.bulk_restored', "Restored {$count} product(s)");

        return redirect()->route('admin.products.index', ['trashed' => 1])
            ->with('success', "{$count} product(s) restored.");
    }

    public function bulkForceDestroy(Request $request)
    {
        $ids = $this->validatedIds($request, trashed: true);

        $products = Product::onlyTrashed()->with('images')->whereIn('id', $ids)->get();

        $deleted = 0;
        $skipped = 0;
        foreach ($products as $product) {
            try {
                $this->purge($product);
                $deleted++;
            } catch (\Illuminate\Database\QueryException $e) {
                report($e);
                $skipped++;
            }
        }

        ActivityLogger::log('product.bulk_force_deleted', "Permanently deleted {$deleted} product(s)");

        $message = "{$deleted} product(s) permanently deleted.";
        if ($skipped > 0) {
            $message .= " {$skipped} could not be deleted (linked records).";
        }

        return redirect()->route('admin.products.index', ['trashed' => 1])
            ->with($deleted > 0 ? 'success' : 'error', $message);
    }

    private function validatedIds(Request $request, bool $trashed = false): array
    {
        return $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ])['ids'];
    }

    /** Permanently remove a product and its stored image files. */
    private function purge(Product $product): void
    {
        foreach ($product->images as $image) {
            if (! Str::startsWith($image->path, 'http')) {
                Storage::disk('public')->delete($image->path);
            }
        }
        $product->forceDelete();
    }

    // ── Images ──────────────────────────────────────────────────────
    public function uploadImage(Request $request, Product $product)
    {
        $request->validate(['image' => ['required', 'image', 'max:8192']]);

        $product->images()->create([
            'path' => $request->file('image')->store('products', 'public'),
            'position' => $product->images()->max('position') + 1,
            'is_primary' => $product->images()->count() === 0,
        ]);

        return back()->with('success', 'Image uploaded.');
    }

    public function deleteImage(ProductImage $image)
    {
        if (! Str::startsWith($image->path, 'http')) {
            Storage::disk('public')->delete($image->path);
        }
        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    public function reorderImages(Request $request)
    {
        foreach ($request->input('order', []) as $position => $id) {
            ProductImage::where('id', $id)->update([
                'position' => $position,
                'is_primary' => $position == 0,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    // ── Helpers ─────────────────────────────────────────────────────
    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'sku' => ['required', 'string', 'max:80', Rule::unique('products', 'sku')->ignore($product)],
            'barcode' => ['nullable', 'string', 'max:80'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'specifications' => ['nullable', 'string'],
            'shipping_info' => ['nullable', 'string'],
            'return_info' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'tax_class' => ['required', 'string', 'max:40'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'track_inventory' => ['boolean'],
            'allow_backorder' => ['boolean'],
            'status' => ['required', Rule::in(['draft', 'active', 'archived'])],
            'is_featured' => ['boolean'],
            'is_new_arrival' => ['boolean'],
            'is_best_seller' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:400'],
            'tags' => ['nullable', 'string', 'max:300'],
            'images.*' => ['nullable', 'image', 'max:8192'],
        ]);

        foreach (['track_inventory', 'allow_backorder', 'is_featured', 'is_new_arrival', 'is_best_seller'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        $data['tags'] = array_values(array_filter(array_map('trim', explode(',', (string) $request->input('tags')))));
        unset($data['images']);

        return $data;
    }

    private function syncInventory(Product $product, Request $request): void
    {
        if ($product->has_variants) {
            return;
        }

        Inventory::updateOrCreate(
            ['product_id' => $product->id, 'product_variant_id' => null],
            [
                'quantity' => (int) $request->input('stock_quantity', 0),
                'low_stock_threshold' => (int) $request->input('low_stock_threshold', 5),
            ],
        );
    }

    private function handleImageUploads(Product $product, Request $request): void
    {
        $files = array_filter((array) $request->file('images', []));

        foreach ($files as $file) {
            try {
                $path = $file->store('products', 'public');

                abort_if($path === false, 500);

                $product->images()->create([
                    'path' => $path,
                    'position' => (int) $product->images()->max('position') + 1,
                    'is_primary' => $product->images()->count() === 0,
                ]);
            } catch (\Throwable $e) {
                report($e);
                session()->flash('error',
                    'The product was saved, but an image could not be stored. '
                    .'Check that storage/app/public is writable on the server.');
            }
        }
    }
}
