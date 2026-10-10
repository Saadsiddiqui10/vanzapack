<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Phone-sized (6:5) versions of the two hero banners, shown below 640px wide. */
return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'banners/vanzapack-hero-1.webp' => 'banners/vanzapack-hero-1-mobile.webp',
            'banners/vanzapack-hero-2.webp' => 'banners/vanzapack-hero-2-mobile.webp',
        ] as $desktop => $mobile) {
            DB::table('banners')->where('image', $desktop)->update(['mobile_image' => $mobile, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        DB::table('banners')->whereIn('mobile_image', [
            'banners/vanzapack-hero-1-mobile.webp',
            'banners/vanzapack-hero-2-mobile.webp',
        ])->update(['mobile_image' => null]);
    }
};
