<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Product Templates — source of truth for available product templates
        Schema::create('product_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Template Slots — slot definitions for each template
        Schema::create('product_template_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('product_templates')->cascadeOnDelete();
            $table->string('slot_key', 100);
            $table->string('slot_type', 50);
            $table->string('name');
            $table->string('view_name', 50);
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->float('default_x')->default(0);
            $table->float('default_y')->default(0);
            $table->float('default_width')->default(100);
            $table->float('default_height')->default(100);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['template_id', 'slot_key']);
        });

        // Add slot_key and slot_type to existing print_areas
        Schema::table('print_areas', function (Blueprint $table) {
            $table->string('slot_key', 100)->nullable()->after('area_type');
            $table->string('slot_type', 50)->nullable()->after('slot_key');
        });

        // Add product_template to products
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_template', 50)->nullable()->after('print_cost_type');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_template');
        });

        Schema::table('print_areas', function (Blueprint $table) {
            $table->dropColumn(['slot_key', 'slot_type']);
        });

        Schema::dropIfExists('product_template_slots');
        Schema::dropIfExists('product_templates');
    }
};
