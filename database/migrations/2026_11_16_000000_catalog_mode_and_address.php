<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * - catalog_mode: replace "Add to cart" with a WhatsApp "Ask for price" button
 *   (toggle in Admin → Settings to bring online ordering back).
 * - Short public address.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'catalog_mode'],
            ['value' => '1', 'group' => 'store', 'type' => 'bool', 'created_at' => now(), 'updated_at' => now()],
        );
        DB::table('settings')->updateOrInsert(
            ['key' => 'store_address'],
            ['value' => 'Jebel Ali, UAE.', 'group' => 'general', 'type' => 'string', 'updated_at' => now()],
        );
        Cache::forget('settings.all');
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'catalog_mode')->delete();
        Cache::forget('settings.all');
    }
};
