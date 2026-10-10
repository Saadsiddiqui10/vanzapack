<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/** Single inbox for all store emails: contact form, newsletter sign-ups, new orders, low stock. */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'notification_email'],
            ['value' => 'sales@vanzapack.com', 'group' => 'general', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
        );
        Cache::forget('settings.all');
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'notification_email')->delete();
        Cache::forget('settings.all');
    }
};
