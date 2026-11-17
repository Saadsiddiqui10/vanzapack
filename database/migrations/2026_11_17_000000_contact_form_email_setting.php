<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/** Contact-form messages go to their own inbox (orders / newsletter alerts keep using notification_email). */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'contact_form_email'],
            ['value' => 'vanzapack@gmail.com', 'group' => 'general', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
        );
        Cache::forget('settings.all');
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'contact_form_email')->delete();
        Cache::forget('settings.all');
    }
};
