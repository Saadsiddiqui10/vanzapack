<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('phone');
            $table->boolean('must_change_password')->default(false)->after('is_active');
            $table->string('customer_group')->default('retail')->after('must_change_password');
            $table->timestamp('last_login_at')->nullable()->after('customer_group');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'phone', 'is_active', 'must_change_password',
                'customer_group', 'last_login_at',
            ]);
        });
    }
};
