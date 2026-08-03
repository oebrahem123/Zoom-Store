<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintArea extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Architecture Convention: slot_key Ownership
    |--------------------------------------------------------------------------
    |
    | Every Fabric canvas object must eventually carry a slot_key property.
    | The slot_key is the permanent owner of editor objects and the foundation
    | of intelligent product switching.
    |
    | slot_key values are globally stable, permanent, English identifiers.
    | They must never depend on translated names or UI labels.
    |
    | view_name is only a descriptive label for UI display.
    | view_index is the functional identifier used by the editor.
    |
    */

    protected $fillable = [
        'product_id',
        'view_name',
        'view_index',
        'name',
        'area_type',
        'slot_key',
        'slot_type',
        'comment',
        'x',
        'y',
        'width',
        'height',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
