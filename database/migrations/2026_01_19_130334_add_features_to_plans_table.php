<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('billing_access')->default(false);
            $table->boolean('advanced_permissions')->default(false);
            // já tens has_priority_support no create_plans_table, não repetir
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['billing_access', 'advanced_permissions']);
        });
    }
};
