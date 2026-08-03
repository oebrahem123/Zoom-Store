<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adjustment 1: Replace string product_template with foreign key product_template_id
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_template');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('product_template_id')->nullable()->after('print_cost_type');
            $table->foreign('product_template_id')->references('id')->on('product_templates')->nullOnDelete();
        });

        // Adjustment 2: Add view_index to template slots
        Schema::table('product_template_slots', function (Blueprint $table) {
            $table->unsignedInteger('view_index')->default(0)->after('view_name');
        });

        // Adjustment 2 (consistency): Add view_index to print_areas too
        Schema::table('print_areas', function (Blueprint $table) {
            $table->unsignedInteger('view_index')->nullable()->after('view_name');
        });
    }

    public function down(): void
    {
        Schema::table('print_areas', function (Blueprint $table) {
            $table->dropColumn('view_index');
        });

        Schema::table('product_template_slots', function (Blueprint $table) {
            $table->dropColumn('view_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['product_template_id']);
            $table->dropColumn('product_template_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('product_template', 50)->nullable()->after('print_cost_type');
        });
    }
};
