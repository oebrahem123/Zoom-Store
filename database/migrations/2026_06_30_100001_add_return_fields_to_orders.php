<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('return_status')->nullable()->after('shipping_saved');
            $table->timestamp('return_requested_at')->nullable()->after('return_status');
            $table->timestamp('returned_at')->nullable()->after('return_requested_at');
            $table->timestamp('refunded_at')->nullable()->after('returned_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['return_status', 'return_requested_at', 'returned_at', 'refunded_at']);
        });
    }
};
