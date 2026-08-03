<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('is_designable');
            $table->index('type');
        });

        Schema::table('print_areas', function (Blueprint $table) {
            $table->index('slot_key');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->index('variant_id');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->index(['product_id', 'size', 'color']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
            $table->dropIndex(['is_designable']);
            $table->dropIndex(['type']);
        });

        Schema::table('print_areas', function (Blueprint $table) {
            $table->dropIndex(['slot_key']);
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['variant_id']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'size', 'color']);
        });
    }
};
