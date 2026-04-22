<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * عند حذف المنتج من لوحة التحكم: الإبقاء على صف السلة/تفاصيل الطلب مع product_id = null
     * والاعتماد على أعمدة الـ snapshot المحفوظة مسبقاً.
     */
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        DB::statement('ALTER TABLE carts MODIFY product_id BIGINT UNSIGNED NULL');

        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });

        Schema::table('orderdetails', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        DB::statement('ALTER TABLE orderdetails MODIFY product_id BIGINT UNSIGNED NULL');

        Schema::table('orderdetails', function (Blueprint $table) {
            if (! Schema::hasColumn('orderdetails', 'product_name')) {
                $table->string('product_name')->nullable();
            }
            if (! Schema::hasColumn('orderdetails', 'product_image')) {
                $table->string('product_image')->nullable();
            }
        });

        Schema::table('orderdetails', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orderdetails', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('orderdetails', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'product_image']);
        });

        DB::statement('ALTER TABLE orderdetails MODIFY product_id BIGINT UNSIGNED NOT NULL');

        Schema::table('orderdetails', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        DB::statement('ALTER TABLE carts MODIFY product_id BIGINT UNSIGNED NOT NULL');

        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }
};
