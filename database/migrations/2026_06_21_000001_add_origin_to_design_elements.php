<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('design_elements', function (Blueprint $table) {
            $table->string('origin_x', 10)->nullable()->after('z_index');
            $table->string('origin_y', 10)->nullable()->after('origin_x');
        });
    }

    public function down(): void
    {
        Schema::table('design_elements', function (Blueprint $table) {
            $table->dropColumn(['origin_x', 'origin_y']);
        });
    }
};
