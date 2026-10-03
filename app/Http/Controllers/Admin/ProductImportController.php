<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductImportController extends Controller
{
    private const COLUMNS = [
        'sku', 'name', 'category', 'brand', 'price', 'sale_price', 'cost_price',
        'stock', 'low_stock_threshold', 'status', 'short_description', 'description',
        'weight', 'barcode', 'is_featured', 'is_new_arrival', 'is_best_seller', 'tags',
    ];

    public function form()
    {
        return view('admin.products.import', [
            'columns' => self::COLUMNS,
        ]);
    }

    public function template()
    {
        $sample = [
            'GC-EXAMPLE-1', 'Kraft Takeaway Box Medium', 'Kraft Boxes & Containers', 'KraftWorks',
            '35.00', '29.00', '18.00', '120', '15', 'active',
            'Grease-resistant kraft box for hot food.', '<p>Full HTML description here.</p>',
            '0.25', '1234567890123', '1', '1', '0', 'eco,takeaway,wholesale',
        ];

        return response()->streamDownload(function () use ($sample) {
            $out = fopen('php://output', 'w');
            fputcsv($out, self::COLUMNS);
            fputcsv($out, $sample);
            fclose($out);
        }, 'vanzapack-product-import-template.csv', ['Content-Type' => 'text/csv']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
            'update_existing' => ['boolean'],
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($handle);

        if (! $header) {
            fclose($handle);

            return back()->with('error', 'The file appears to be empty.');
        }

        $header = array_map(fn ($h) => Str::of($h)->trim()->lower()->replace(' ', '_')->value(), $header);

        if (! in_array('sku', $header, true) || ! in_array('name', $header, true)) {
            fclose($handle);

            return back()->with('error', 'CSV must contain at least "sku" and "name" columns. Download the template for the correct format.');
        }

        $updateExisting = $request->boolean('update_existing');
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
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

            if ($sku === '' || $name === '') {
                $errors[] = "Row {$line}: missing SKU or name.";
                $skipped++;

                continue;
            }

            $categoryId = $this->resolveCategory($data['category'] ?? null, $categories);
            if (! $categoryId) {
                $errors[] = "Row {$line} ({$sku}): category \"".($data['category'] ?? '')."\" not found.";
                $skipped++;

                continue;
            }

            $existing = Product::withTrashed()->where('sku', $sku)->first();
            if ($existing && ! $updateExisting) {
                $skipped++;

                continue;
            }

            try {
                DB::transaction(function () use ($data, $sku, $name, $categoryId, $brands, $existing, &$created, &$updated) {
                    $attributes = [
                        'name' => $name,
                        'category_id' => $categoryId,
                        'brand_id' => $this->resolveBrand($data['brand'] ?? null, $brands),
                        'price' => (float) ($data['price'] ?? 0),
                        'sale_price' => $this->nullableFloat($data['sale_price'] ?? null),
                        'cost_price' => $this->nullableFloat($data['cost_price'] ?? null),
                        'short_description' => $data['short_description'] ?? null,
                        'description' => $data['description'] ?? null,
                        'weight' => (float) ($data['weight'] ?? 0),
                        'barcode' => $data['barcode'] ?? null,
                        'status' => in_array($data['status'] ?? '', ['active', 'draft', 'archived'], true) ? $data['status'] : 'active',
                        'is_featured' => $this->bool($data['is_featured'] ?? null),
                        'is_new_arrival' => $this->bool($data['is_new_arrival'] ?? null),
                        'is_best_seller' => $this->bool($data['is_best_seller'] ?? null),
                        'tags' => $this->tags($data['tags'] ?? null),
                        'track_inventory' => true,
                    ];

                    if ($existing) {
                        $existing->fill($attributes)->save();
                        if ($existing->trashed()) {
                            $existing->restore();
                        }
                        $product = $existing;
                        $updated++;
                    } else {
                        $attributes['sku'] = $sku;
                        $attributes['slug'] = Str::slug($name).'-'.Str::lower(Str::random(5));
                        $product = Product::create($attributes);
                        $created++;
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
                });
            } catch (\Throwable $e) {
                report($e);
                $errors[] = "Row {$line} ({$sku}): ".Str::limit($e->getMessage(), 120);
                $skipped++;
            }
        }

        fclose($handle);

        ActivityLogger::log('product.imported', "Imported products: {$created} created, {$updated} updated");

        return view('admin.products.import', [
            'columns' => self::COLUMNS,
            'result' => compact('created', 'updated', 'skipped', 'errors'),
        ]);
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
