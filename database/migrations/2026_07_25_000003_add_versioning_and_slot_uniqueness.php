<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convention 2: Template versioning preparation
        Schema::table('product_templates', function (Blueprint $table) {
            $table->unsignedInteger('version')->default(1)->after('key');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('template_version')->nullable()->after('product_template_id');
        });

        // Convention 4: slot_key is permanent and unique per template
        Schema::table('product_template_slots', function (Blueprint $table) {
            $table->unique(['template_id', 'slot_key']);
        });
    }

    public function down(): void
    {
        Schema::table('product_template_slots', function (Blueprint $table) {
            $table->dropUnique(['template_id', 'slot_key']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('template_version');
        });

        Schema::table('product_templates', function (Blueprint $table) {
            $table->dropColumn('version');
        });
    }
};
