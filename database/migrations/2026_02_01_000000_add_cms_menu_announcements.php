<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Announcement bar messages ──────────────────────────────
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->string('url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        // ── Header navigation menu ─────────────────────────────────
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url')->nullable();          // path or full URL
            $table->string('type')->default('link');    // link | mega
            $table->boolean('is_active')->default(true);
            $table->boolean('open_in_new_tab')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        // ── Brands: homepage feature + ordering ────────────────────
        Schema::table('brands', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->unsignedInteger('position')->default(0)->after('is_featured');
        });

        // ── Seed sensible defaults ─────────────────────────────────
        $now = now();

        DB::table('announcements')->insert([
            ['text' => 'Enjoy Free Delivery on Orders Above AED 99', 'url' => '/shop', 'is_active' => true, 'position' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['text' => '10% OFF your first order — code WELCOME10', 'url' => null, 'is_active' => true, 'position' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('menu_items')->insert([
            ['label' => 'Home', 'url' => '/', 'type' => 'link', 'position' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Shop by Category', 'url' => null, 'type' => 'mega', 'position' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'New Arrivals', 'url' => '/new-arrivals', 'type' => 'link', 'position' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Best Sellers', 'url' => '/best-sellers', 'type' => 'link', 'position' => 4, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Offers', 'url' => '/offers', 'type' => 'link', 'position' => 5, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Brands', 'url' => '/brands', 'type' => 'link', 'position' => 6, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'About', 'url' => '/page/about-us', 'type' => 'link', 'position' => 7, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Contact', 'url' => '/contact', 'type' => 'link', 'position' => 8, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Feature the first 8 active brands on the homepage by default.
        $brandIds = DB::table('brands')->where('is_active', true)->orderBy('name')->limit(8)->pluck('id');
        foreach ($brandIds as $i => $id) {
            DB::table('brands')->where('id', $id)->update(['is_featured' => true, 'position' => $i + 1]);
        }
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'position']);
        });
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('announcements');
    }
};
