<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $settings = [
        ['store_email', 'info@vanzapack.com', 'general'],
        ['store_sales_email', 'sales@vanzapack.com', 'general'],
        ['store_support_email', 'support@vanzapack.com', 'general'],
        ['social_linkedin', 'https://www.linkedin.com/in/vanza-pack-852122441/', 'social'],
    ];

    public function up(): void
    {
        foreach ($this->settings as [$key, $value, $group]) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'group' => $group, 'type' => 'string', 'updated_at' => now(), 'created_at' => now()],
            );
        }

        Cache::forget('settings.all');
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['store_sales_email', 'store_support_email'])->delete();
        Cache::forget('settings.all');
    }
};
