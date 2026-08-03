<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_potos', function (Blueprint $table) {
            $table->string('view_name')->nullable()->after('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('product_potos', function (Blueprint $table) {
            $table->dropColumn('view_name');
        });
    }
};
