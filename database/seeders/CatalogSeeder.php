<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    private array $attributeValues = [];

    public function run(): void
    {
        $this->seedAttributes();
        $brands = $this->seedBrands();
        $categories = $this->seedCategories();
        $this->seedProducts($categories, $brands);
    }

    private function img(string $label, string $bg = 'f4f7ee', string $fg = '2c3d13'): string
    {
        return "https://placehold.co/800x800/{$bg}/{$fg}?text=".rawurlencode($label);
    }

    private function seedAttributes(): void
    {
        $size = Attribute::create(['name' => 'Size', 'type' => 'select', 'is_variation' => true, 'position' => 1]);
        foreach (['Small', 'Medium', 'Large', 'X-Large'] as $i => $v) {
            $this->attributeValues['Size'][$v] = AttributeValue::create([
                'attribute_id' => $size->id, 'value' => $v, 'position' => $i,
            ]);
        }

        $cup = Attribute::create(['name' => 'Cup Size', 'type' => 'select', 'is_variation' => true, 'position' => 2]);
        foreach (['4 OZ', '8 OZ', '12 OZ', '16 OZ'] as $i => $v) {
            $this->attributeValues['Cup Size'][$v] = AttributeValue::create([
                'attribute_id' => $cup->id, 'value' => $v, 'position' => $i,
            ]);
        }

        $colour = Attribute::create(['name' => 'Colour', 'type' => 'color', 'is_variation' => true, 'position' => 3]);
        foreach (['Kraft' => '#b0854a', 'White' => '#ffffff', 'Black' => '#1a1a1a', 'Brown' => '#6f4a2f'] as $v => $hex) {
            $this->attributeValues['Colour'][$v] = AttributeValue::create([
                'attribute_id' => $colour->id, 'value' => $v, 'color_hex' => $hex,
            ]);
        }

        $pack = Attribute::create(['name' => 'Pack Size', 'type' => 'select', 'is_variation' => true, 'position' => 4]);
        foreach (['50 Pcs', '100 Pcs', '500 Pcs', '1000 Pcs'] as $i => $v) {
            $this->attributeValues['Pack Size'][$v] = AttributeValue::create([
                'attribute_id' => $pack->id, 'value' => $v, 'position' => $i,
            ]);
        }
    }

    private function seedBrands(): array
    {
        $brands = [
            'EcoServe' => 'Compostable bagasse and moulded-fibre food packaging.',
            'PureLeaf' => 'Plant-based cutlery, straws and tableware.',
            'KraftWorks' => 'Recycled kraft bags, boxes and pouches for takeaway.',
            'Al Fajr' => 'Premium facial tissue and away-from-home paper products.',
            'MonoPack' => 'PP and PET cups, lids and portion pots.',
            'GreenLine' => 'Everyday hygiene, gloves and cleaning essentials.',
            'Barista Basics' => 'Syrups, coffee sundries and beverage ingredients.',
            'AluPro' => 'Aluminium foil containers, rolls and lids.',
        ];

        $result = [];
        foreach ($brands as $name => $desc) {
            $result[$name] = Brand::create([
                'name' => $name,
                'description' => $desc,
                'website' => 'https://'.Str::slug($name).'.example.com',
                'logo' => $this->img($name, 'ffffff', '000066'),
                'is_active' => true,
                'meta_title' => "{$name} products | VanzaPack",
                'meta_description' => $desc,
            ]);
        }

        return $result;
    }

    private function seedCategories(): array
    {
        $tree = [
            'Food Packaging' => [
                'featured' => true,
                'children' => ['Bagasse Products', 'Paper Cups', 'Paper Bags & Pouches', 'Kraft Boxes & Containers'],
            ],
            'Tableware & Cutlery' => [
                'featured' => true,
                'children' => ['Wooden Cutlery', 'Plastic Cutlery', 'Plates & Bowls'],
            ],
            'Hygiene & Cleaning' => [
                'featured' => true,
                'children' => ['Facial Tissues', 'Toilet Rolls', 'Gloves', 'Cleaning Tools'],
            ],
            'Food & Grocery' => [
                'featured' => true,
                'children' => ['Cooking Oil', 'Syrups & Beverages', 'Baking Ingredients'],
            ],
            'Aluminium Products' => ['featured' => false, 'children' => []],
            'Corrugated & Shipping' => ['featured' => false, 'children' => []],
        ];

        $flat = [];
        $position = 0;
        foreach ($tree as $name => $config) {
            $parent = Category::create([
                'name' => $name,
                'description' => "Explore our range of {$name} for restaurants, cafes and retailers.",
                'image' => $this->img($name, 'e6f0d9'),
                'banner' => $this->img($name.' collection', 'd7e8bf'),
                'is_active' => true,
                'is_featured' => $config['featured'],
                'position' => $position++,
                'meta_title' => "{$name} | VanzaPack UAE",
                'meta_description' => "Buy {$name} in bulk with fast UAE delivery from VanzaPack.",
            ]);
            $flat[$name] = $parent;

            foreach ($config['children'] as $childPos => $childName) {
                $flat[$childName] = Category::create([
                    'parent_id' => $parent->id,
                    'name' => $childName,
                    'description' => "Quality {$childName} at wholesale prices.",
                    'image' => $this->img($childName, 'eef4e2'),
                    'is_active' => true,
                    'position' => $childPos,
                    'meta_title' => "{$childName} | VanzaPack UAE",
                    'meta_description' => "Shop {$childName} online at VanzaPack.",
                ]);
            }
        }

        return $flat;
    }

    private function seedProducts(array $categories, array $brands): void
    {
        // [name, category, brand, price, sale, short, variantAttr(null|Size|Cup Size|Colour|Pack Size), flags]
        $catalog = [
            ['Corrugated Cup Holder 2 Cup / 4 Cup Carrier', 'Kraft Boxes & Containers', 'KraftWorks', 18.00, 15.00, 'Sturdy moulded-fibre carrier that keeps hot and cold drinks stable in transit.', 'Size', ['featured', 'best_seller']],
            ['White Twisted Handle Paper Bag — Shopping Takeaway', 'Paper Bags & Pouches', 'KraftWorks', 35.00, 25.00, 'Premium white kraft carrier bag with reinforced twisted handles.', 'Size', ['best_seller', 'new']],
            ['Brown Paper Bags with Twisted Handles', 'Paper Bags & Pouches', 'KraftWorks', 25.00, 20.00, 'Natural kraft bags for bakeries, delis and boutique retail.', 'Size', ['best_seller']],
            ['PET Juice Cups with Flat Dome Lids', 'Paper Cups', 'MonoPack', 20.00, 15.00, 'Crystal-clear recyclable PET cups with matching flat or dome lids.', 'Cup Size', ['featured', 'best_seller']],
            ['Double Wall Kraft Coffee Cups', 'Paper Cups', 'EcoServe', 32.00, 27.00, 'Insulated double-wall cups — no sleeve required. Compostable lining.', 'Cup Size', ['featured', 'new']],
            ['White Single Wall Paper Cups', 'Paper Cups', 'EcoServe', 18.00, null, 'Everyday single-wall hot cups for coffee and tea service.', 'Cup Size', []],
            ['Ripple Wall Hot Cups', 'Paper Cups', 'EcoServe', 30.00, 24.00, 'Textured ripple insulation for premium hot beverage presentation.', 'Cup Size', ['new']],
            ['PP Portion Cups with Lids 1 OZ to 4 OZ', 'Paper Cups', 'MonoPack', 12.00, 9.00, 'Leak-resistant portion pots for sauces, dressings and samples.', 'Cup Size', ['best_seller']],
            ['Kraft Salad Bowls with Lids 500ML - 1300ML', 'Bagasse Products', 'EcoServe', 40.00, 30.00, 'Grease-resistant kraft bowls with clear anti-fog lids for salads and poke.', 'Size', ['featured']],
            ['Bagasse Clamshell Burger Box', 'Bagasse Products', 'EcoServe', 28.00, 23.00, 'Sugarcane-fibre clamshell — sturdy, microwavable and home-compostable.', 'Size', ['best_seller']],
            ['Bagasse 3-Compartment Meal Tray', 'Bagasse Products', 'EcoServe', 34.00, null, 'Keeps mains and sides separate. Ideal for catering and canteens.', null, []],
            ['Bagasse Round Plates 7" / 9" / 10"', 'Plates & Bowls', 'EcoServe', 22.00, 18.00, 'Rigid compostable plates that hold up to hot, oily and saucy foods.', 'Size', ['best_seller']],
            ['Kraft Meal Box with Window', 'Kraft Boxes & Containers', 'KraftWorks', 45.00, 38.00, 'Food-safe kraft box with PLA window for sandwiches, wraps and bakery.', 'Size', ['new']],
            ['Kraft Pizza Box 9" to 14"', 'Kraft Boxes & Containers', 'KraftWorks', 55.00, 44.00, 'B-flute corrugated pizza box with vent holes and clean print surface.', 'Size', ['featured']],
            ['Wooden Cutlery Set — Fork, Knife, Spoon, Napkin', 'Wooden Cutlery', 'PureLeaf', 26.00, 21.00, 'FSC birch cutlery kit wrapped with a kraft napkin. Fully compostable.', 'Pack Size', ['best_seller', 'new']],
            ['Birch Wooden Forks', 'Wooden Cutlery', 'PureLeaf', 14.00, null, 'Smooth-sanded wooden forks — no splinters, no plastic.', 'Pack Size', []],
            ['Birch Wooden Spoons', 'Wooden Cutlery', 'PureLeaf', 14.00, null, 'Sturdy wooden spoons for desserts, soups and salads.', 'Pack Size', []],
            ['CPLA Compostable Cutlery', 'Plastic Cutlery', 'PureLeaf', 30.00, 24.00, 'Heat-tolerant CPLA cutlery that looks and feels like premium plastic.', 'Pack Size', ['new']],
            ['Heavy-Duty PP Cutlery', 'Plastic Cutlery', 'MonoPack', 20.00, 16.00, 'Reusable-grade polypropylene cutlery for high-volume service.', 'Pack Size', []],
            ['Kraft Paper Straws', 'Plates & Bowls', 'PureLeaf', 16.00, 12.00, 'Durable 6mm paper straws that hold their shape in cold drinks.', 'Pack Size', ['best_seller']],
            ['Al Fajr Facial Tissue 200 Sheets 2 Ply', 'Facial Tissues', 'Al Fajr', 35.00, 29.00, 'Soft 2-ply facial tissue — cube box for counters and tables.', 'Pack Size', ['featured', 'best_seller']],
            ['Al Fajr Facial Tissue 150 Sheets 2 Ply', 'Facial Tissues', 'Al Fajr', 18.00, 14.70, 'Compact 2-ply facial tissue box for guest rooms and desks.', null, ['best_seller']],
            ['Fresh Facial Tissue 500 Sheets 2 Ply', 'Facial Tissues', 'Al Fajr', 33.00, 26.00, 'High-count flat box — fewer refills for busy washrooms.', null, ['new']],
            ['Boutique Facial Tissue 2 Ply — 6 Box Pack', 'Facial Tissues', 'Al Fajr', 20.00, 15.00, 'Décor-friendly boutique cube tissue, multipack.', null, []],
            ['Maxi Roll 2 Ply — 6 Roll Pack', 'Toilet Rolls', 'GreenLine', 40.00, 33.00, 'Long-lasting hard-wound maxi roll for high-traffic washrooms.', null, ['best_seller']],
            ['Toilet Roll 2 Ply — 10 Roll Pack', 'Toilet Rolls', 'GreenLine', 22.00, 18.00, 'Everyday soft 2-ply toilet tissue, economy multipack.', null, []],
            ['Kitchen Paper Towel Roll — 2 Pack', 'Toilet Rolls', 'GreenLine', 15.00, null, 'Highly absorbent kitchen towel for spills and food prep.', null, []],
            ['Vinyl Gloves Powder-Free — 100 Pcs', 'Gloves', 'GreenLine', 20.00, 17.00, 'Food-safe powder-free vinyl gloves for prep and service.', 'Size', ['best_seller', 'featured']],
            ['Nitrile Gloves Powder-Free — 100 Pcs', 'Gloves', 'GreenLine', 32.00, 27.00, 'Puncture-resistant nitrile gloves for kitchens and cleaning.', 'Size', ['new']],
            ['Black Nitrile Gloves — 100 Pcs', 'Gloves', 'GreenLine', 35.00, 28.00, 'Professional black nitrile gloves — barista and back-of-house favourite.', 'Size', []],
            ['Microfibre Cleaning Cloths — 12 Pack', 'Cleaning Tools', 'GreenLine', 24.00, 19.00, 'Lint-free microfibre cloths for glass, steel and surfaces.', 'Colour', []],
            ['Spin Mop & Bucket Set', 'Cleaning Tools', 'GreenLine', 85.00, 69.00, 'Wring-as-you-go spin mop with heavy-duty bucket.', null, ['featured']],
            ['Wet Wipes Soft & Refreshing 6x8cm', 'Cleaning Tools', 'GreenLine', 15.00, 10.00, 'Individually usable refreshing wipes for tables and hands.', 'Pack Size', ['best_seller']],
            ['Aluminium Foil Container 8389 with Lids', 'Aluminium Products', 'AluPro', 30.00, 24.00, 'Full-curl aluminium containers with board lids for hot takeaway.', 'Pack Size', ['best_seller']],
            ['Aluminium Foil Roll 300m Catering', 'Aluminium Products', 'AluPro', 45.00, 38.00, 'Heavy-gauge catering foil roll in a cutter box.', null, []],
            ['Cling Film Food Wrap 300m', 'Aluminium Products', 'AluPro', 28.00, null, 'PVC-free cling film with slide cutter for food prep stations.', null, []],
            ['Virgin Olive Oil 4L Food Service', 'Cooking Oil', 'Barista Basics', 115.00, 110.00, 'Extra-virgin olive oil in a catering 4L tin — cold pressed.', null, ['best_seller']],
            ['Vegetable Cooking Oil 17L', 'Cooking Oil', 'Barista Basics', 128.00, 118.00, 'All-purpose frying oil for high-volume kitchens.', null, []],
            ['Vanilla Syrup 1L for Coffee', 'Syrups & Beverages', 'Barista Basics', 76.00, 69.00, 'Barista-grade vanilla syrup for lattes and iced drinks.', null, ['featured']],
            ['Caramel Syrup 1L', 'Syrups & Beverages', 'Barista Basics', 76.00, 69.00, 'Rich caramel syrup — a café bar essential.', null, []],
            ['Organic Coconut Water 1L — 6 Pack', 'Syrups & Beverages', 'Barista Basics', 76.00, 68.00, 'Not-from-concentrate organic coconut water, case of 6.', null, ['new']],
            ['Belgian Milk Chocolate Chips 500G', 'Baking Ingredients', 'Barista Basics', 39.00, 36.00, '33.6% cocoa Belgian chocolate chips for baking and drinks.', null, ['best_seller']],
            ['Panko Bread Crumbs 4mm 1KG', 'Baking Ingredients', 'Barista Basics', 17.00, 14.50, 'Light, crisp Japanese-style panko for coatings.', null, []],
            ['Golden Syrup 454g', 'Baking Ingredients', 'Barista Basics', 14.50, 10.50, 'Classic rich golden syrup for baking and desserts.', null, ['best_seller']],
            ['Corrugated Shipping Box — Medium', 'Corrugated & Shipping', 'KraftWorks', 60.00, 48.00, 'Double-wall corrugated box for e-commerce and transit.', 'Size', []],
            ['Kraft Wrap Box for Sandwiches', 'Corrugated & Shipping', 'KraftWorks', 42.00, 34.00, 'Fold-flat kraft wrap box that speaks quality at the counter.', 'Size', ['new']],
        ];

        $skuSeq = 1000;
        foreach ($catalog as [$name, $categoryName, $brandName, $price, $sale, $short, $variantAttr, $flags]) {
            $category = $categories[$categoryName];
            $brand = $brands[$brandName] ?? null;
            $hasVariants = $variantAttr !== null;

            $product = Product::create([
                'category_id' => $category->id,
                'brand_id' => $brand?->id,
                'name' => $name,
                'sku' => 'VP-'.(++$skuSeq),
                'barcode' => (string) fake()->ean13(),
                'short_description' => $short,
                'description' => "<p>{$short}</p><p>".fake()->paragraph(4).'</p><p>'.fake()->paragraph(3).'</p>',
                'specifications' => '<ul><li>Material: food-grade, FDA compliant</li><li>Origin: UAE / EU</li><li>Storage: cool, dry place</li><li>Case quantity varies by pack size</li></ul>',
                'shipping_info' => '<p>Dispatched within 1-2 business days. Free delivery across the UAE on orders above AED 99.</p>',
                'return_info' => '<p>Unopened cases can be returned within 7 days. See our Return Policy for details.</p>',
                'price' => $price,
                'sale_price' => $sale,
                'cost_price' => round($price * 0.6, 2),
                'weight' => fake()->randomFloat(3, 0.1, 6),
                'has_variants' => $hasVariants,
                'track_inventory' => true,
                'status' => 'active',
                'is_featured' => in_array('featured', $flags),
                'is_new_arrival' => in_array('new', $flags),
                'is_best_seller' => in_array('best_seller', $flags),
                'tags' => ['eco', 'wholesale', 'uae'],
                'meta_title' => "{$name} | VanzaPack UAE",
                'meta_description' => Str::limit($short, 150),
                'sales_count' => fake()->numberBetween(0, 400),
                'views' => fake()->numberBetween(20, 5000),
                'published_at' => now()->subDays(fake()->numberBetween(1, 120)),
            ]);

            // images
            foreach (range(1, 3) as $i) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $this->img(Str::limit($name, 22, ''), $i === 1 ? 'f4f7ee' : 'eef3e6'),
                    'alt' => $name,
                    'is_primary' => $i === 1,
                    'position' => $i - 1,
                ]);
            }

            if (! $hasVariants) {
                Inventory::create([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'quantity' => in_array('best_seller', $flags) ? fake()->numberBetween(40, 90) : fake()->numberBetween(0, 300),
                    'reserved' => 0,
                    'low_stock_threshold' => 15,
                ]);

                continue;
            }

            $values = $this->attributeValues[$variantAttr];
            $vPos = 0;
            foreach ($values as $label => $value) {
                $delta = $vPos * fake()->randomFloat(2, 1.5, 6);
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $product->sku.'-'.Str::slug($label),
                    'barcode' => (string) fake()->ean13(),
                    'name' => $label,
                    'price' => round($price + $delta, 2),
                    'sale_price' => $sale ? round($sale + $delta, 2) : null,
                    'cost_price' => round(($price + $delta) * 0.6, 2),
                    'weight' => fake()->randomFloat(3, 0.1, 6),
                    'is_active' => true,
                    'position' => $vPos++,
                ]);
                $variant->attributeValues()->attach($value->id);
                $product->attributeValues()->syncWithoutDetaching([$value->id]);

                Inventory::create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => fake()->numberBetween(0, 120),
                    'reserved' => 0,
                    'low_stock_threshold' => 10,
                ]);
            }
        }
    }
}
