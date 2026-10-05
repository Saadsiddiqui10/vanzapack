<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $settings = [
        ['store_phone', '+971 4 262 7225', 'general'],
        ['store_landline', '+971 4 262 7225', 'general'],
        ['store_address', 'Down Town Jebel Ali St 19, JAFZA View, 1st Floor Tower 18, Dubai, United Arab Emirates', 'general'],
        ['whatsapp_country_code', '971', 'whatsapp'],
        ['whatsapp_phone', '505021026', 'whatsapp'],
        ['whatsapp_default_message', 'Hello, Good Day. I would like to connect with the Vanza Pack Web Sales Team regarding your products and services', 'whatsapp'],
    ];

    public function up(): void
    {
        foreach ($this->settings as [$key, $value, $group]) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'group' => $group, 'type' => 'string', 'updated_at' => now()],
            );
        }

        Cache::forget('settings.all');
    }

    public function down(): void
    {
        //
    }
};
