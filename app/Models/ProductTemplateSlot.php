<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTemplateSlot extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Architecture Convention: slot_key Permanence
    |--------------------------------------------------------------------------
    |
    | slot_key is a permanent, stable, English identifier for a print area slot.
    | It must never depend on translated names, UI labels, or display text.
    | The same slot_key may appear across multiple templates (e.g. front_main
    | in tshirt, hoodie, polo) — each representing the same conceptual slot.
    |
    | view_name is only a descriptive label for UI display.
    | view_index is the functional identifier used by the editor internally.
    |
    */

    protected $fillable = [
        'template_id',
        'slot_key',
        'slot_type',
        'name',
        'view_name',
        'view_index',
        'is_required',
        'display_order',
        'default_x',
        'default_y',
        'default_width',
        'default_height',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ProductTemplate::class, 'template_id');
    }
}
