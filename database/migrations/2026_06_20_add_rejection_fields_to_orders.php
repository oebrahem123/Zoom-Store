<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('note');
            $table->timestamp('rejected_at')->nullable()->after('status');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
            $table->string('rejection_category')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['status', 'rejected_at', 'rejection_reason', 'rejection_category']);
        });
    }
};
