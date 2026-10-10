<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LoadCatalog extends Command
{
    protected $signature = 'vanzapack:load-catalog
        {--fresh : Wipe existing products & categories first}
        {--per-leaf=3 : Products to generate per lowest-level category}
        {--no-images : Skip generating demo images}';

    protected $description = 'Build the full packaging category tree and generate a realistic product catalogue';

    private int $sku = 10000;

    private int $productCount = 0;

    private int $categoryCount = 0;

    /**
     * Parent → Section (mid category) → leaf sub-categories.
     * A section with an empty array is itself a browsable leaf.
     */
    private function tree(): array
    {
        return [
            'Paper Products' => [
                'Paper Cups' => ['Kraft Paper Cups', 'Ripple Paper Cups', 'Printed Paper Cups', 'Single Wall Paper Cups', 'Double Wall Paper Cups', 'Embossed Paper Cups', 'White Paper Cups', 'Paper Cone Cups'],
                'Cup Carriers' => [],
                'Paper Cups Lids' => ['Flat Paper Lids', 'Dome Paper Lids', 'Sipper Paper Lids'],
                'Paper Bowls' => ['Kraft Salad Bowls', 'Paper Ice Cream Bowls', 'Soup Bowls', 'Noodle Bowls'],
                'Paper Bags' => ['Flat Handle Bags', 'Kraft Bags', 'Gift Paper Bag', 'White Bags', 'Flat Bottom Bags', 'Twisted Handle Bags', 'Square Bottom Bags', 'Paper Cookies Bag', 'Paper Bag With Window', 'PE Coated Chicken Bag', 'Tin-Tie Bag'],
                'Paper Boxes' => ['Kraft Salad Boxes', 'Kraft Sandwich Box', 'Pizza Boxes & Liners', 'Kraft Lunch Box', 'Kraft Lunch Box With Window', 'Paper Meal Box', 'Pail Box', 'Kids Meal Box'],
                'Paper Trays' => ['Kraft Boat Tray', 'White Boat Trays', 'Open Ended Trays'],
                'Paper Doilies' => [],
                'Popcorn Tubs' => [],
            ],
            'Tissue Products' => [
                'Maxi Rolls' => [], 'Facial Tissues' => [], 'Paper Napkins' => [], 'Toilet Rolls' => [],
                'C-Fold Tissues' => [], 'V-Fold Tissues' => [], 'Wet Tissues' => [], 'Kitchen Rolls' => [], 'Bed Rolls' => [],
            ],
            'Plastic Products' => [
                'PET Products' => ['PET Cups', 'PET Dome Lids', 'PET Salad Containers', 'PET Deli Cups'],
                'Plastic Bowls' => ['Microwavable Bowls', 'Plastic Salad Bowls', 'Plastic Ice Cream Bowls', 'Red & Black Bowl'],
                'Plastic Cups & Bottles' => ['Juice Cups', 'Portion Cups', 'Juice Bottles'],
                'Plastic Rolls' => ['Sofra Table Sheet Roll', 'Vegetable Rolls'],
                'Plastic Bags' => ['Grill Chicken Bags', 'Piping Bags', 'Multi Purpose Bags', 'Juice Bags', 'Zipper Lock Bags', 'Cup Carrier Bag', 'Dustbin Bags', 'Garbage Bag Rolls', 'Drawstring Bags', 'Heavy Duty Garbage Bags', 'Carry Bags'],
                'Plastic Takeaway Containers' => ['Black Base Containers', 'Microwave Containers', 'Red & Black Container', 'Sushi Containers', 'Deli Containers'],
                'Plastic Stirrers & Straws' => ['Plastic Stirrers', 'Plastic Straws'],
                'Cling Film Food Wrap' => [],
                'Plastic Cutlery' => ['Plastic Heavy Duty Cutlery', 'Plastic Medium Duty Cutlery', 'Plastic Light Duty Cutlery', 'Plastic Cutlery Set', 'Plastic Ice Cream Spoon'],
                'Plastic Plates & Trays' => ['Crystal Plates', 'Plastic Plates', 'Microwavable Plate', 'Plastic Rectangular Trays'],
                'Prayer Mat' => [],
            ],
            'Hygiene & Protection' => [
                'Face Masks' => ['Disposable Face Mask', 'Disposable Face Shield', 'Kids Face Mask'],
                'Gloves' => ['Nitrile Gloves', 'Vinyl Gloves', 'Plastic Gloves'],
                'Protective Wears' => ['Chef Hats', 'Protective Nurse Caps', 'Shoe Covers'],
                'Cleaning Accessories' => ['Biohazard Waste Bags', 'Toilet Seat Covers'],
                'Hygiene Combo' => [],
            ],
            'Baking & Decoration' => [
                'Cake Boxes' => [], 'Cake Boards' => [], 'Muffin & Cupcake Cases' => [], 'Baking Paper' => [],
                'Piping Bags & Nozzles' => [], 'Cake Dowels' => [], 'Decoration Doilies' => [],
            ],
            'Bio-Degradable Products' => [
                'Bagasse Plates' => [], 'Bagasse Bowls' => [], 'Bagasse Containers' => [], 'Bagasse Trays' => [],
                'Cornstarch Cutlery' => [], 'CPLA Cutlery' => [], 'Kraft Paper Straws' => [], 'Wooden Cutlery' => [], 'Palm Leaf Plates' => [],
            ],
            'Aluminium Products' => [
                'Aluminium Foils' => [], 'Aluminium Containers' => [], 'Aluminium Platters' => [], 'Foil Wrap' => [],
            ],
            'Wooden Products' => [
                'Wooden Forks' => [], 'Wooden Spoons' => [], 'Wooden Knives' => [], 'Wooden Cutlery Sets' => [],
                'Wooden Stirrers' => [], 'Wooden Chopsticks' => [], 'Wooden Skewers' => [], 'Wooden Coffee Stirrers' => [],
            ],
            'Dispensers' => [
                'Tissue Dispensers' => [], 'Soap Dispensers' => [], 'Cup Dispensers' => [],
                'Gloves Dispensers' => [], 'Nurse Cap Dispensers' => [], 'Foil & Wrap Dispensers' => [],
            ],
            'Combo Packs' => [
                'Restaurant Starter Combo' => [], 'Cafe Essentials Combo' => [], 'Cleaning Combo' => [], 'Hygiene Combo Pack' => [],
            ],
            'New Arrivals' => [],
        ];
    }

    public function handle(): int
    {
        if ($this->option('fresh') && $this->confirm('This wipes ALL current products and categories. Continue?', true)) {
            $this->wipe();
        }

        DB::transaction(function () {
            $position = 0;
            foreach ($this->tree() as $parentName => $sections) {
                $parent = $this->category($parentName, null, $position++, isFeatured: $position <= 8);

                if (empty($sections)) {
                    $this->generateProducts($parent, $parentName);

                    continue;
                }

                $sp = 0;
                foreach ($sections as $sectionName => $leaves) {
                    $section = $this->category($sectionName, $parent->id, $sp++);

                    if (empty($leaves)) {
                        $this->generateProducts($section, $parentName);

                        continue;
                    }

                    $lp = 0;
                    foreach ($leaves as $leafName) {
                        $leaf = $this->category($leafName, $section->id, $lp++);
                        $this->generateProducts($leaf, $parentName);
                    }
                }
            }
        });

        $this->newLine();
        $this->info("Catalogue loaded: {$this->categoryCount} categories, {$this->productCount} products.");
        $this->line('Review it in the admin panel, then export (see the guide printed below).');

        return self::SUCCESS;
    }

    private function wipe(): void
    {
        $this->warn('Wiping…');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach (['product_images', 'product_variants', 'attribute_value_product', 'attribute_value_product_variant',
            'inventory_movements', 'inventories', 'wishlist_items', 'cart_items', 'reviews', 'review_images',
            'coupon_usages', 'order_status_histories', 'order_items', 'payments', 'orders',
            'recently_viewed_products', 'products', 'categories'] as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        Storage::disk('public')->deleteDirectory('catalog');
    }

    private function category(string $name, ?int $parentId, int $position, bool $isFeatured = false): Category
    {
        $slug = Str::slug($name).($parentId ? '-'.substr(md5($name.$parentId), 0, 4) : '');

        $category = Category::withTrashed()->firstOrNew(['slug' => $slug]);
        $category->fill([
            'name' => $name,
            'parent_id' => $parentId,
            'is_active' => true,
            'is_featured' => $isFeatured && $parentId === null,
            'position' => $position,
            'description' => "Shop our range of {$name} — wholesale pricing, fast UAE delivery.",
            'meta_title' => "{$name} | VanzaPack UAE",
            'meta_description' => "Buy {$name} online in bulk from VanzaPack.",
        ]);
        if ($category->trashed()) {
            $category->restore();
        }
        $category->save();

        if ($category->wasRecentlyCreated) {
            $this->categoryCount++;
        }
        $this->makeImage("catalog/categories/{$category->id}.svg", $name, 'Category', $category, 'image', wide: true);

        return $category;
    }

    private function generateProducts(Category $category, string $rootName): void
    {
        if ($this->option('fresh') === false && $category->products()->exists()) {
            return;
        }
        if ($category->name === 'New Arrivals') {
            return;
        }

        $per = max(1, (int) $this->option('per-leaf'));
        $specs = $this->specsFor($category->name);

        foreach (array_slice($specs, 0, max($per, count($specs) >= $per ? $per : count($specs))) as $i => [$suffix, $price, $blurb, $variantAxis]) {
            $name = trim($category->name.' '.$suffix);
            $sku = 'VP-'.(++$this->sku);
            $onSale = random_int(0, 100) < 35;

            $product = Product::create([
                'category_id' => $category->id,
                'name' => $name,
                'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
                'sku' => $sku,
                'barcode' => (string) random_int(1000000000000, 9999999999999),
                'short_description' => $blurb,
                'description' => "<p>{$blurb}</p><ul><li>Food-grade, FDA compliant material</li><li>Bulk cartons for busy kitchens</li><li>Fast delivery across the UAE</li></ul>",
                'specifications' => '<ul><li>Category: '.$rootName.'</li><li>Origin: UAE / Import</li><li>Storage: cool, dry place</li></ul>',
                'shipping_info' => '<p>Dispatched in 1–2 business days. Free delivery on orders above AED 99.</p>',
                'return_info' => '<p>Unopened cartons returnable within 7 days.</p>',
                'price' => $price,
                'sale_price' => $onSale ? round($price * (random_int(70, 90) / 100), 2) : null,
                'cost_price' => round($price * 0.6, 2),
                'weight' => round(random_int(50, 6000) / 1000, 3),
                'has_variants' => (bool) $variantAxis,
                'track_inventory' => true,
                'status' => 'active',
                'is_featured' => random_int(0, 100) < 12,
                'is_new_arrival' => random_int(0, 100) < 20,
                'is_best_seller' => random_int(0, 100) < 15,
                'tags' => ['wholesale', 'uae', Str::slug($rootName)],
                'meta_title' => "{$name} | VanzaPack UAE",
                'meta_description' => Str::limit($blurb, 150),
                'sales_count' => random_int(0, 300),
                'views' => random_int(10, 4000),
                'published_at' => now()->subDays(random_int(1, 200)),
            ]);
            $this->productCount++;

            $this->makeImage("catalog/products/{$product->id}-0.svg", $name, $rootName, $product, isProductImage: true, primary: true);
            $this->makeImage("catalog/products/{$product->id}-1.svg", $name, $rootName, $product, isProductImage: true, primary: false, variant: 1);

            if ($variantAxis) {
                $this->makeVariants($product, $variantAxis, $price, $onSale);
            } else {
                Inventory::create([
                    'product_id' => $product->id,
                    'quantity' => random_int(0, 400),
                    'reserved' => 0,
                    'low_stock_threshold' => 15,
                ]);
            }
        }
    }

    private function makeVariants(Product $product, array $axis, float $base, bool $onSale): void
    {
        [$attrName, $options] = $axis;
        foreach ($options as $pos => $opt) {
            $delta = $pos * round($base * 0.12, 2);
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $product->sku.'-'.Str::slug($opt),
                'barcode' => (string) random_int(1000000000000, 9999999999999),
                'name' => $opt,
                'price' => round($base + $delta, 2),
                'sale_price' => $onSale ? round(($base + $delta) * 0.85, 2) : null,
                'cost_price' => round(($base + $delta) * 0.6, 2),
                'weight' => round(random_int(50, 5000) / 1000, 3),
                'is_active' => true,
                'position' => $pos,
            ]);
            Inventory::create([
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'quantity' => random_int(0, 200),
                'reserved' => 0,
                'low_stock_threshold' => 10,
            ]);
        }
    }

    /**
     * @return array<int,array{0:string,1:float,2:string,3:?array{0:string,1:array<int,string>}}>
     */
    private function specsFor(string $leaf): array
    {
        $l = Str::lower($leaf);

        $cupSizes = ['Cup Size', ['4 OZ', '8 OZ', '12 OZ', '16 OZ']];
        $packSizes = ['Pack Size', ['50 Pcs', '100 Pcs', '500 Pcs', '1000 Pcs']];
        $garmentSizes = ['Size', ['Small', 'Medium', 'Large', 'X-Large']];
        $boxSizes = ['Size', ['Small', 'Medium', 'Large']];

        return match (true) {
            str_contains($l, 'lid') => [
                ['4 OZ', 9, 'Snug-fit paper lid for hot and cold cups.', null],
                ['8 OZ', 11, 'Leak-resistant paper lid, compostable liner.', null],
                ['12–16 OZ', 13, 'Universal paper lid for medium and large cups.', null],
            ],
            str_contains($l, 'cup') && ! str_contains($l, 'carrier') => [
                ['', 18, 'Everyday paper cup for coffee, tea and cold drinks.', $cupSizes],
                ['Bulk Carton', 95, 'Case quantity for high-volume service.', $cupSizes],
            ],
            str_contains($l, 'carrier') => [
                ['2-Cup', 15, 'Moulded-fibre carrier that keeps 2 drinks stable in transit.', null],
                ['4-Cup', 18, 'Sturdy 4-cup drink carrier for takeaway and delivery.', null],
            ],
            str_contains($l, 'bag') => [
                ['', 22, 'Strong takeaway bag for food service and retail.', $boxSizes],
                ['Bulk Carton', 120, 'Case pack for busy outlets.', $boxSizes],
            ],
            str_contains($l, 'box') || str_contains($l, 'tray') || str_contains($l, 'container') || str_contains($l, 'bowl') || str_contains($l, 'tub') => [
                ['', 26, 'Food-safe, grease-resistant and stackable.', $boxSizes],
                ['With Lids', 34, 'Includes matching anti-fog lids.', $boxSizes],
                ['Bulk Carton', 140, 'Case quantity for kitchens and canteens.', null],
            ],
            str_contains($l, 'plate') => [
                ['7 inch', 16, 'Rigid plate that holds up to hot and saucy foods.', null],
                ['9 inch', 20, 'Full-size dinner plate for catering and events.', null],
                ['3-Compartment', 28, 'Keeps mains and sides separate.', null],
            ],
            str_contains($l, 'cutlery') || str_contains($l, 'fork') || str_contains($l, 'spoon') || str_contains($l, 'knife') || str_contains($l, 'stirrer') || str_contains($l, 'straw') || str_contains($l, 'chopstick') || str_contains($l, 'skewer') => [
                ['', 16, 'Smooth-finish and sturdy for everyday service.', $packSizes],
                ['Wrapped', 24, 'Individually wrapped for hygiene.', $packSizes],
            ],
            str_contains($l, 'glove') => [
                ['Powder-Free', 20, 'Food-safe powder-free gloves for prep and service.', $garmentSizes],
                ['Box of 100', 32, 'Dispenser box of 100 gloves.', $garmentSizes],
            ],
            str_contains($l, 'mask') || str_contains($l, 'shield') || str_contains($l, 'cap') || str_contains($l, 'hat') || str_contains($l, 'cover') => [
                ['50 Pcs', 18, 'Disposable protective wear, 50-piece pack.', null],
                ['Bulk Box', 60, 'Case pack for clinics and kitchens.', null],
            ],
            str_contains($l, 'foil') || str_contains($l, 'wrap') || str_contains($l, 'film') || str_contains($l, 'roll') || str_contains($l, 'sheet') => [
                ['Catering Roll 300m', 45, 'Heavy-gauge catering roll in a cutter box.', null],
                ['Standard 30cm', 22, 'Everyday width for food prep stations.', null],
                ['Pop-Up Sheets', 28, 'Interfolded pre-cut sheets for fast service.', null],
            ],
            str_contains($l, 'tissue') || str_contains($l, 'napkin') || str_contains($l, 'towel') => [
                ['1 Ply', 14, 'Soft, absorbent single-ply tissue.', $packSizes],
                ['2 Ply', 20, 'Premium 2-ply for tables and washrooms.', $packSizes],
            ],
            str_contains($l, 'dispenser') => [
                ['Wall-Mounted', 55, 'Durable ABS dispenser, easy refill.', null],
                ['Counter-Top', 48, 'Compact counter-top dispenser.', null],
            ],
            str_contains($l, 'doily') || str_contains($l, 'doilies') => [
                ['Round', 15, 'Lace-edge paper doilies for presentation.', null],
                ['Rectangular', 17, 'Tray-liner doilies for buffets.', null],
            ],
            str_contains($l, 'combo') => [
                ['Starter Pack', 149, 'A ready-to-go bundle of essentials for new outlets.', null],
                ['Value Pack', 249, 'Bulk bundle at the best per-unit price.', null],
            ],
            default => [
                ['', 24, 'Quality packaging supply at wholesale prices.', $packSizes],
                ['Bulk Carton', 110, 'Case quantity for busy businesses.', null],
            ],
        };
    }

    // ── Demo image (SVG) ────────────────────────────────────────────
    private function makeImage(string $path, string $title, string $subtitle, $model, string $field = 'image', bool $isProductImage = false, bool $primary = false, int $variant = 0, bool $wide = false): void
    {
        if ($this->option('no-images')) {
            return;
        }

        Storage::disk('public')->put($path, $this->svg($title, $subtitle, $variant, $wide));

        if ($isProductImage) {
            ProductImage::create([
                'product_id' => $model->id,
                'path' => $path,
                'alt' => $title,
                'is_primary' => $primary,
                'position' => $variant,
            ]);
        } else {
            $model->forceFill([$field => $path])->saveQuietly();
        }
    }

    private function svg(string $title, string $subtitle, int $variant, bool $wide): string
    {
        $w = 800;
        $h = $wide ? 450 : 800;
        $cx = $w / 2;
        $palettes = [['#eef4e2', '#7aa82f'], ['#e6f2cf', '#5e8226'], ['#f3f9e8', '#95c93f'], ['#e8eef7', '#000066'], ['#f4f7ee', '#3f5620']];
        [$bg, $accent] = $palettes[hexdec(substr(md5($subtitle.$title), 0, 2)) % count($palettes)];

        $words = preg_split('/\s+/', trim($title));
        $lines = [];
        $line = '';
        foreach ($words as $word) {
            if (mb_strlen($line.' '.$word) > ($wide ? 24 : 18) && $line !== '') {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $line === '' ? $word : $line.' '.$word;
            }
        }
        $line !== '' && $lines[] = $line;
        $lines = array_slice($lines, 0, 3);

        $top = ($h - count($lines) * 52) / 2 + ($wide ? 20 : 90);
        $text = '';
        foreach ($lines as $i => $l) {
            $text .= '<text x="'.$cx.'" y="'.($top + $i * 52).'" text-anchor="middle" font-family="Poppins, Segoe UI, Arial, sans-serif" font-size="40" font-weight="700" fill="#2c3d13">'.htmlspecialchars($l, ENT_QUOTES).'</text>';
        }
        $iconX = $cx - 46;
        $iconY = $top - 150;
        $sub = htmlspecialchars(Str::limit($subtitle, 24, ''), ENT_QUOTES);

        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$w} {$h}" width="{$w}" height="{$h}">
          <defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{$bg}"/><stop offset="1" stop-color="#ffffff"/></linearGradient></defs>
          <rect width="{$w}" height="{$h}" fill="url(#g)"/>
          <rect x="600" y="-40" width="230" height="230" rx="40" fill="{$accent}" opacity="0.08" transform="rotate(18 700 80)"/>
          <g transform="translate({$iconX}, {$iconY})">
            <rect width="92" height="92" rx="20" fill="{$accent}"/>
            <path d="M18 30 L46 16 L74 30 L74 62 L46 78 L18 62 Z" fill="none" stroke="#ffffff" stroke-width="5" stroke-linejoin="round"/>
            <path d="M18 30 L46 44 L74 30 M46 44 L46 78" stroke="#ffffff" stroke-width="5" fill="none"/>
          </g>
          {$text}
          <text x="{$cx}" y="{$h}" dy="-58" text-anchor="middle" font-family="Figtree, Segoe UI, Arial, sans-serif" font-size="24" fill="#5e8226">{$sub}</text>
          <text x="{$cx}" y="{$h}" dy="-26" text-anchor="middle" font-family="Figtree, Segoe UI, Arial, sans-serif" font-size="15" letter-spacing="2" fill="#94a3b8">VANZAPACK</text>
        </svg>
        SVG;
    }
}
