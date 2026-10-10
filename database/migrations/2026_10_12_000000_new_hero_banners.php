<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Two distinct hero banners (WhatsApp number + vanzapack.com baked in).
 * Slide 1 keeps the "Packing Material" design, slide 2 is the new food-packaging design.
 */
return new class extends Migration
{
    private array $slides = [
        [
            'image' => 'banners/vanzapack-hero-1.webp',
            'title' => 'Packing Material — Quality Packing Solutions for Homes, Offices & Businesses',
            'subtitle' => 'Boxes, tapes, bubble wrap and everything you need to pack, protect and deliver.',
            'cta_label' => 'Shop Now',
            'cta_url' => '/shop',
        ],
        [
            'image' => 'banners/vanzapack-hero-2.webp',
            'title' => 'Everything Your Food Business Needs',
            'subtitle' => 'Cups, containers, bags, cutlery & more — in bulk, at wholesale prices, delivered across the UAE.',
            'cta_label' => 'Shop Now',
            'cta_url' => '/shop',
        ],
    ];

    public function up(): void
    {
        $existing = DB::table('banners')->where('placement', 'hero')->orderBy('position')->orderBy('id')->pluck('id')->all();

        foreach ($this->slides as $i => $slide) {
            $values = $slide + ['is_active' => true, 'position' => $i, 'updated_at' => now()];

            if (isset($existing[$i])) {
                DB::table('banners')->where('id', $existing[$i])->update($values);
            } else {
                DB::table('banners')->insert($values + ['placement' => 'hero', 'created_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
