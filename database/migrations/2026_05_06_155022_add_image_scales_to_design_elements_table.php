<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageScalesToDesignElementsTable extends Migration
{
    public function up()
    {
        Schema::table('design_elements', function (Blueprint $table) {
            $table->float('scale_x')->nullable()->after('height');
            $table->float('scale_y')->nullable()->after('scale_x');
            $table->integer('original_width')->nullable()->after('scale_y');
            $table->integer('original_height')->nullable()->after('original_width');
        });
    }

    public function down()
    {
        Schema::table('design_elements', function (Blueprint $table) {
            $table->dropColumn(['scale_x', 'scale_y', 'original_width', 'original_height']);
        });
    }
}
