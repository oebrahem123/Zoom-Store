<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_areas', function (Blueprint $table) {
            $table->string('view_name')->nullable()->after('product_id');
            $table->string('area_type')->nullable()->after('name');
            $table->text('comment')->nullable()->after('area_type');
        });
    }

    public function down(): void
    {
        Schema::table('print_areas', function (Blueprint $table) {
            $table->dropColumn(['view_name', 'area_type', 'comment']);
        });
    }
};
