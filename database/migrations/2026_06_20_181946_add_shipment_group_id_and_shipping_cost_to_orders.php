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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipment_group_id')->nullable()->constrained('shipment_groups')->nullOnDelete();
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('shipment_group_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipment_group_id');
            $table->dropColumn('shipping_cost');
        });
    }
};
