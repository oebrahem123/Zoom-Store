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
        Schema::create('design_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained('custom_designs')->cascadeOnDelete();
            $table->string('type'); // text, image, badge
            $table->text('content')->nullable(); // text or image path
            $table->float('position_x');
            $table->float('position_y');
            $table->float('width')->nullable();
            $table->float('height')->nullable();
            $table->float('rotation')->default(0);
            $table->string('color')->nullable();
            $table->string('font_family')->nullable();
            $table->integer('z_index')->default(0);
            $table->integer('view')->default(0); // 0=front, 1=back, etc
            $table->foreignId('print_area_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_elements');
    }
};
