<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignElement extends Model
{
    protected $fillable = [
        'design_id',
        'view',
        'type',
        'content',
        'position_x',
        'position_y',
        'width',
        'height',
        'rotation',
        'color',
        'font_family',
        'z_index',
        'print_area_id',
        'scale_x',
        'scale_y',
        'original_width',
        'original_height',
        'origin_x',
        'origin_y',
        'metadata',
    ];

    protected $casts = [
        'scale_x' => 'float',
        'scale_y' => 'float',
        'original_width' => 'integer',
        'original_height' => 'integer',
        'position_x' => 'float',
        'position_y' => 'float',
        'width' => 'integer',
        'height' => 'integer',
        'rotation' => 'integer',
        'z_index' => 'integer',
        'metadata' => 'array',
    ];

    public function design()
    {
        return $this->belongsTo(CustomDesign::class, 'design_id');
    }

    public function printArea()
    {
        return $this->belongsTo(PrintArea::class, 'print_area_id');
    }
}
