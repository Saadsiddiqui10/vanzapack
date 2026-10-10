<x-admin-layout title="Import Products" active="products">
    <x-admin.head title="Import products &amp; photos" :back="route('admin.products.index')" />

    @isset($result)
        <div class="mb-6 rounded-xl border border-slate-200 bg-white p-5">
            <h3 class="mb-2 font-semibold text-brand-800">Import finished</h3>
            <div class="flex flex-wrap gap-4 text-sm">
                <span class="rounded-lg bg-brand-50 px-3 py-1.5 text-brand-800">{{ $result['created'] }} created</span>
                <span class="rounded-lg bg-sky-50 px-3 py-1.5 text-sky-800">{{ $result['updated'] }} updated</span>
                <span class="rounded-lg bg-amber-50 px-3 py-1.5 text-amber-800">{{ $result['images'] }} photos added</span>
                <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-slate-600">{{ $result['skipped'] }} skipped</span>
            </div>
            @if(!empty($result['errors']))
                <details class="mt-3 text-sm" open>
                    <summary class="cursor-pointer font-medium text-rose-600">{{ count($result['errors']) }} issue(s)</summary>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-slate-600">
                        @foreach(array_slice($result['errors'], 0, 100) as $err)<li>{{ $err }}</li>@endforeach
                    </ul>
                </details>
            @endif
            <a href="{{ route('admin.products.index') }}" class="btn-primary mt-4 py-2 text-sm">Back to products</a>
        </div>
    @endisset

    @if($missingPhotos > 0)
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm">
            <p class="text-amber-900">
                <strong>{{ $missingPhotos }} {{ Str::plural('product', $missingPhotos) }}</strong> only {{ $missingPhotos === 1 ? 'has' : 'have' }} a placeholder image or no photo.
                Download the list, fill in the <code class="rounded bg-white px-1">images</code> column, and import it below.
            </p>
            <a href="{{ route('admin.products.import.missing-photos') }}" class="btn-outline bg-white py-2 text-sm">Download products missing photos</a>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <form method="POST" action="{{ route('admin.products.import') }}" enctype="multipart/form-data"
              x-data="{ busy: false }" @submit="busy = true"
              class="space-y-4 rounded-xl border border-slate-200 bg-white p-6">
            @csrf
            <x-admin.field label="CSV file" name="file" hint="Max 5 MB. UTF-8, comma-separated. Optional if you only upload a ZIP of photos.">
                <input type="file" name="file" accept=".csv,text/csv"
                       class="block w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:font-medium file:text-brand-700">
            </x-admin.field>

            <x-admin.field label="Photos ZIP (optional)" name="images_zip"
                           hint="A .zip of JPG / PNG / WEBP photos. Photos are matched by the file names in the CSV “images” column, or automatically when named after the SKU (VP-10001.jpg, VP-10001-2.jpg) or the product name.">
                @if($zipSupported)
                    <input type="file" name="images_zip" accept=".zip,application/zip"
                           class="block w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:font-medium file:text-brand-700">
                @else
                    <p class="rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-700">ZIP upload is unavailable: the PHP “zip” extension is disabled on this server. Use full image URLs in the CSV instead.</p>
                @endif
            </x-admin.field>

            <label class="flex items-start gap-2 text-sm">
                <input type="checkbox" name="update_existing" value="1" checked class="mt-0.5 rounded border-slate-300 text-brand-500">
                <span>Update products that already exist (matched by SKU). Only the columns in your CSV are changed.</span>
            </label>

            <label class="flex items-start gap-2 text-sm">
                <input type="checkbox" name="replace_images" value="1" class="mt-0.5 rounded border-slate-300 text-brand-500">
                <span>Replace all existing photos of a product <span class="text-slate-400">(otherwise new photos are added; placeholder images are always replaced)</span></span>
            </label>

            <button class="btn-primary" :disabled="busy">
                <span x-show="!busy">Import</span>
                <span x-show="busy" x-cloak>Importing… large photo sets can take a few minutes</span>
            </button>
        </form>

        <div class="rounded-xl border border-slate-200 bg-white p-6 text-sm">
            <h3 class="mb-2 font-semibold text-brand-800">CSV format</h3>
            <p class="text-slate-500">First row must be the header. Only <code class="rounded bg-slate-100 px-1">sku</code> is always required; new products also need <code class="rounded bg-slate-100 px-1">name</code> and <code class="rounded bg-slate-100 px-1">category</code>. To add photos to existing products, a CSV with just <code class="rounded bg-slate-100 px-1">sku</code> and <code class="rounded bg-slate-100 px-1">images</code> is enough.</p>
            <a href="{{ route('admin.products.import.template') }}" class="btn-outline mt-3 py-2 text-sm">Download template CSV</a>

            <table class="mt-4 w-full">
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach([
                        'sku' => 'Unique product code (required)',
                        'name' => 'Product name (required for new products)',
                        'category' => 'Category name (must already exist) or ID',
                        'price' => 'Regular price, e.g. 35.00',
                        'sale_price' => 'Optional discounted price (leave empty to remove)',
                        'cost_price' => 'Optional cost',
                        'stock' => 'Stock quantity (simple products)',
                        'low_stock_threshold' => 'Low-stock alert level (default 5)',
                        'status' => 'active / draft / archived (default active)',
                        'short_description' => 'One-line summary',
                        'description' => 'Full description (HTML allowed)',
                        'weight' => 'Weight in kg',
                        'barcode' => 'Barcode / EAN',
                        'is_featured' => '1 or 0',
                        'is_new_arrival' => '1 or 0',
                        'is_best_seller' => '1 or 0',
                        'tags' => 'Comma-separated, e.g. "eco,bulk"',
                        'images' => 'Photo file names from the ZIP and/or full https:// image URLs, separated by | — the first one becomes the main photo',
                    ] as $col => $desc)
                        <tr><td class="py-1.5 pr-3 font-mono text-brand-700">{{ $col }}</td><td class="py-1.5 text-slate-500">{{ $desc }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            <p class="mt-3 text-xs text-slate-400">Photos are resized to max 1600px and saved as WebP. Products with variants are not created by import — add those manually. Import updates the base product + simple stock only.</p>
        </div>
    </div>
</x-admin-layout>
