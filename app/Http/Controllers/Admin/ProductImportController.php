<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Services\ProductImageImporter;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductImportController extends Controller
{
    private const COLUMNS = [
        'sku', 'name', 'category', 'brand', 'price', 'sale_price', 'cost_price',
        'stock', 'low_stock_threshold', 'status', 'short_description', 'description',
        'weight', 'barcode', 'is_featured', 'is_new_arrival', 'is_best_seller', 'tags', 'images',
    ];

    public function form()
    {
        return view('admin.products.import', [
            'columns' => self::COLUMNS,
            'zipSupported' => ProductImageImporter::zipSupported(),
            'missingPhotos' => $this->missingPhotosQuery()->count(),
        ]);
    }

    public function template()
    {
        $sample = [
            'GC-EXAMPLE-1', 'Kraft Takeaway Box Medium', 'Kraft Boxes & Containers', 'KraftWorks',
            '35.00', '29.00', '18.00', '120', '15', 'active',
            'Grease-resistant kraft box for hot food.', '<p>Full HTML description here.</p>',
            '0.25', '1234567890123', '1', '1', '0', 'eco,takeaway,wholesale',
            'kraft-box-front.jpg | https://example.com/photos/kraft-box-side.jpg',
        ];

        return $this->csvDownload('vanzapack-product-import-template.csv', self::COLUMNS, [$sample]);
    }

    /** Products that only have generated placeholder images (or none), ready to fill in the "images" column. */
    public function missingPhotos()
    {
        $rows = $this->missingPhotosQuery()->with('category:id,name')->orderBy('id')->get()
            ->map(fn (Product $p) => [$p->sku, $p->name, $p->category?->name, '']);

        return $this->csvDownload('vanzapack-products-missing-photos.csv', ['sku', 'name', 'category', 'images'], $rows);
    }

    public function import(Request $request, ProductImageImporter $images)
    {
        $request->validate([
            'file' => ['nullable', 'required_without:images_zip', 'file', 'mimes:csv,txt', 'max:5120'],
            'images_zip' => ['nullable', 'file', 'mimes:zip', 'max:512000'],
            'update_existing' => ['boolean'],
            'replace_images' => ['boolean'],
        ], [
            'file.required_without' => 'Choose a CSV file, a ZIP of photos, or both.',
        ]);

        @set_time_limit(0);

        $replaceImages = $request->boolean('replace_images');
        $result = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'images' => 0, 'errors' => []];

        if ($request->hasFile('images_zip')) {
            if (! ProductImageImporter::zipSupported()) {
                return back()->with('error', 'This server cannot read ZIP files (PHP "zip" extension is disabled). Use image URLs in the CSV instead.');
            }
            if (! $images->openZip($request->file('images_zip')->getRealPath())) {
                return back()->with('error', 'The ZIP file could not be opened. Please re-create it and try again.');
            }
        }

        try {
            if ($request->hasFile('file')) {
                $response = $this->importCsv($request, $images, $replaceImages, $result);
                if ($response) {
                    return $response;
                }
            } else {
                $this->importZipOnly($images, $replaceImages, $result);
            }
        } finally {
            $images->close();
        }

        ActivityLogger::log('product.imported', "Imported products: {$result['created']} created, {$result['updated']} updated, {$result['images']} images");

        return view('admin.products.import', [
            'columns' => self::COLUMNS,
            'zipSupported' => ProductImageImporter::zipSupported(),
            'missingPhotos' => $this->missingPhotosQuery()->count(),
            'result' => $result,
        ]);
    }

    private function importCsv(Request $request, ProductImageImporter $images, bool $replaceImages, array &$result)
    {
        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($handle);

        if (! $header) {
            fclose($handle);

            return back()->with('error', 'The file appears to be empty.');
        }

        // Strip a UTF-8 BOM (Excel adds one) and normalise header names.
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
        $header = array_map(fn ($h) => Str::of($h)->trim()->lower()->replace(' ', '_')->value(), $header);

        if (! in_array('sku', $header, true)) {
            fclose($handle);

            return back()->with('error', 'CSV must contain a "sku" column. Download the template for the correct format.');
        }

        $updateExisting = $request->boolean('update_existing');
        $has = fn (string $col) => in_array($col, $header, true);
        $line = 1;

        $categories = Category::pluck('id', 'name')->mapWithKeys(fn ($id, $name) => [mb_strtolower($name) => $id]);
        $brands = Brand::pluck('id', 'name')->mapWithKeys(fn ($id, $name) => [mb_strtolower($name) => $id]);

        while (($row = fgetcsv($handle)) !== false) {
            $line++;
            if (count(array_filter($row, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            $data = array_combine($header, array_pad(array_slice($row, 0, count($header)), count($header), null));
            $sku = trim((string) ($data['sku'] ?? ''));
            $name = trim((string) ($data['name'] ?? ''));
            $context = "Row {$line} ({$sku})";

            if ($sku === '') {
                $result['errors'][] = "Row {$line}: missing SKU.";
                $result['skipped']++;

                continue;
            }

            $existing = Product::withTrashed()->where('sku', $sku)->first();

            if ($existing && ! $updateExisting) {
                // Still allow adding photos to existing products.
                $result['images'] += $this->attachImages($existing, $data, $images, $replaceImages, $result['errors'], $context);
                $result['skipped']++;

                continue;
            }

            if (! $existing && $name === '') {
                $result['errors'][] = "{$context}: new products need a name.";
                $result['skipped']++;

                continue;
            }

            $categoryId = null;
            if (! $existing || trim((string) ($data['category'] ?? '')) !== '') {
                $categoryId = $this->resolveCategory($data['category'] ?? null, $categories);
                if (! $categoryId) {
                    $result['errors'][] = "{$context}: category \"".($data['category'] ?? '')."\" not found.";
                    $result['skipped']++;

                    continue;
                }
            }

            try {
                $product = DB::transaction(function () use ($data, $has, $sku, $name, $categoryId, $brands, $existing, &$result) {
                    // Only columns present in the CSV are written, so a partial CSV
                    // (e.g. just sku + images) never wipes prices or descriptions.
                    $attributes = array_filter([
                        'name' => $name !== '' ? $name : null,
                        'category_id' => $categoryId,
                        'brand_id' => $has('brand') ? $this->resolveBrand($data['brand'] ?? null, $brands) : null,
                        'price' => $has('price') && ($data['price'] ?? '') !== '' ? (float) $data['price'] : null,
                        'sale_price' => $has('sale_price') ? $this->nullableFloat($data['sale_price'] ?? null) : null,
                        'cost_price' => $has('cost_price') ? $this->nullableFloat($data['cost_price'] ?? null) : null,
                        'short_description' => $has('short_description') ? ($data['short_description'] ?? null) : null,
                        'description' => $has('description') ? ($data['description'] ?? null) : null,
                        'weight' => $has('weight') && ($data['weight'] ?? '') !== '' ? (float) $data['weight'] : null,
                        'barcode' => $has('barcode') ? ($data['barcode'] ?? null) : null,
                        'status' => $has('status') && in_array($data['status'] ?? '', ['active', 'draft', 'archived'], true) ? $data['status'] : null,
                        'is_featured' => $has('is_featured') ? $this->bool($data['is_featured'] ?? null) : null,
                        'is_new_arrival' => $has('is_new_arrival') ? $this->bool($data['is_new_arrival'] ?? null) : null,
                        'is_best_seller' => $has('is_best_seller') ? $this->bool($data['is_best_seller'] ?? null) : null,
                        'tags' => $has('tags') ? $this->tags($data['tags'] ?? null) : null,
                    ], fn ($v) => $v !== null);

                    // Clearing a sale price must be possible on update.
                    if ($has('sale_price') && ($data['sale_price'] ?? '') === '') {
                        $attributes['sale_price'] = null;
                    }

                    if ($existing) {
                        $existing->fill($attributes)->save();
                        if ($existing->trashed()) {
                            $existing->restore();
                        }
                        $product = $existing;
                        $result['updated']++;
                    } else {
                        $product = Product::create($attributes + [
                            'sku' => $sku,
                            'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
                            'price' => 0,
                            'weight' => 0,
                            'status' => 'active',
                            'track_inventory' => true,
                            'is_featured' => false,
                            'is_new_arrival' => false,
                            'is_best_seller' => false,
                        ]);
                        $result['created']++;
                    }

                    if (! $product->has_variants && ($data['stock'] ?? '') !== '') {
                        Inventory::updateOrCreate(
                            ['product_id' => $product->id, 'product_variant_id' => null],
                            [
                                'quantity' => (int) $data['stock'],
                                'low_stock_threshold' => (int) ($data['low_stock_threshold'] ?? 5),
                            ],
                        );
                    }

                    return $product;
                });

                $result['images'] += $this->attachImages($product, $data, $images, $replaceImages, $result['errors'], $context);
            } catch (\Throwable $e) {
                report($e);
                $result['errors'][] = "{$context}: ".Str::limit($e->getMessage(), 120);
                $result['skipped']++;
            }
        }

        fclose($handle);

        return null;
    }

    /** ZIP uploaded without a CSV: match every photo to a product by SKU or product name. */
    private function importZipOnly(ProductImageImporter $images, bool $replaceImages, array &$result): void
    {
        $matched = 0;
        Product::query()->select(['id', 'sku', 'name'])->orderBy('id')->chunk(500, function ($products) use ($images, $replaceImages, &$result, &$matched) {
            foreach ($products as $product) {
                $sources = $images->zipSourcesFor($product);
                if ($sources === []) {
                    continue;
                }
                $matched++;
                $added = $images->attach($product, $sources, $replaceImages, $result['errors'], "{$product->sku} ({$product->name})");
                $result['images'] += $added;
                $result['updated'] += $added > 0 ? 1 : 0;
            }
        });

        if ($matched === 0) {
            $result['errors'][] = 'No photo in the ZIP matched a product. Name each photo after the product SKU (e.g. GC-10001.jpg, GC-10001-2.jpg) or the exact product name, or upload a CSV with an "images" column.';
        }
    }

    private function attachImages(Product $product, array $data, ProductImageImporter $images, bool $replace, array &$errors, string $context): int
    {
        $sources = $images->parseCell($data['images'] ?? null);

        // No images listed: fall back to ZIP photos named after the SKU / product name.
        if ($sources === [] && $images->hasZip()) {
            $sources = $images->zipSourcesFor($product);
        }

        return $sources === [] ? 0 : $images->attach($product, $sources, $replace, $errors, $context);
    }

    private function missingPhotosQuery()
    {
        return Product::query()->whereDoesntHave('images', fn ($q) => $q->where('path', 'not like', ProductImageImporter::PLACEHOLDER_PATTERN));
    }

    private function csvDownload(string $filename, array $header, iterable $rows)
    {
        return response()->streamDownload(function () use ($header, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // so Excel opens UTF-8 correctly
            fputcsv($out, $header);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function resolveCategory(?string $value, $map): ?int
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if (ctype_digit($value)) {
            return Category::whereKey($value)->value('id');
        }

        return $map[mb_strtolower($value)] ?? null;
    }

    private function resolveBrand(?string $value, $map): ?int
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if (ctype_digit($value)) {
            return Brand::whereKey($value)->value('id');
        }
        if (isset($map[mb_strtolower($value)])) {
            return $map[mb_strtolower($value)];
        }

        return Brand::create(['name' => $value, 'is_active' => true])->id;
    }

    private function nullableFloat($v): ?float
    {
        return ($v === null || $v === '') ? null : (float) $v;
    }

    private function bool($v): bool
    {
        return in_array(mb_strtolower(trim((string) $v)), ['1', 'yes', 'true', 'y'], true);
    }

    private function tags($v): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $v))));
    }
}
