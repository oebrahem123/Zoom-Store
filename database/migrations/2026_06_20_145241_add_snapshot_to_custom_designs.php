<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('custom_designs', function (Blueprint $table) {
            $table->json('snapshot')->nullable()->after('preview_image');
        });
    }

    public function down(): void
    {
        Schema::table('custom_designs', function (Blueprint $table) {
            $table->dropColumn('snapshot');
        });
    }
};
