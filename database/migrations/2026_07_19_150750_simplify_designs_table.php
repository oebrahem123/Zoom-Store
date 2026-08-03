<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('designs', function (Blueprint $table) {
            $table->dropColumn(['status', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('designs', function (Blueprint $table) {
            $table->boolean('status')->default(true)->after('image');
            $table->integer('sort_order')->default(0)->after('status');
        });
    }
};
