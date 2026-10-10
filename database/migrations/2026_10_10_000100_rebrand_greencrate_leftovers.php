<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Remove leftovers of the old "GreenCrate" name:
 *  - text such as brand SEO titles ("EcoServe products | GreenCrate")
 *  - product / variant SKUs with the "GC-" prefix become "VP-"
 * Past orders keep the SKU they were placed with.
 */
return new class extends Migration
{
    private array $textColumns = [
        'brands' => ['name', 'description', 'meta_title', 'meta_description'],
        'categories' => ['name', 'description', 'meta_title', 'meta_description'],
        'products' => ['name', 'short_description', 'description', 'specifications', 'shipping_info', 'return_info', 'meta_title', 'meta_description'],
        'pages' => ['title', 'content', 'meta_title', 'meta_description'],
        'settings' => ['value'],
        'banners' => ['title', 'subtitle', 'button_text'],
        'announcements' => ['text'],
    ];

    public function up(): void
    {
        foreach ($this->textColumns as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }
                $expr = "`{$column}`";
                foreach (['GreenCrate' => 'VanzaPack', 'Greencrate' => 'VanzaPack', 'GREENCRATE' => 'VANZAPACK', 'greencrate' => 'vanzapack'] as $from => $to) {
                    $expr = "REPLACE({$expr}, '{$from}', '{$to}')";
                }
                DB::table($table)->where($column, 'like', '%crate%')->update([$column => DB::raw($expr)]);
            }
        }

        foreach (['products', 'product_variants'] as $table) {
            DB::table($table)->where('sku', 'like', 'GC-%')
                ->update(['sku' => DB::raw("CONCAT('VP-', SUBSTRING(`sku`, 4))")]);
        }

        Cache::forget('settings.all');
    }

    public function down(): void
    {
        //
    }
};
