<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The stock hero banner showed a placeholder phone number and website.
 * Point it at the corrected image (new file name so browsers drop the cached copy).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('banners')
            ->whereIn('image', [
                'banners/t6aMz6KADrRq8nsNWVnimVarB5knkthBKTJeCAva.png',
                'banners/FOE1N7H1ynsv7WNj9V3rVzK11yJAcFHfteDARKhW.png',
            ])
            ->update(['image' => 'banners/vanzapack-hero-2026-10.png', 'updated_at' => now()]);
    }

    public function down(): void
    {
        //
    }
};
