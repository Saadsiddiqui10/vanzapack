<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Hide "Brands" from the main navigation (can be re-enabled in Admin → Menu). */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('menu_items')->where('url', '/brands')->update(['is_active' => false, 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('menu_items')->where('url', '/brands')->update(['is_active' => true, 'updated_at' => now()]);
    }
};
