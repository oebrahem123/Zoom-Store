<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductTemplate extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Architecture Conventions
    |--------------------------------------------------------------------------
    |
    | 1. slot_key is a mandatory, permanent, globally stable English identifier.
    |    It must never depend on translated names or UI labels.
    |
    | 2. Templates are versioned. A product records the template_version it was
    |    created with. Future template versions must never break older products.
    |
    | 3. Templates referenced by products must never be physically deleted.
    |    Use is_active = false instead. Old products must always remain functional.
    |
    */

    protected $fillable = [
        'name',
        'key',
        'version',
        'description',
        'display_order',
        'is_active',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function slots(): HasMany
    {
        return $this->hasMany(ProductTemplateSlot::class, 'template_id');
    }

    public function requiredSlots(): HasMany
    {
        return $this->slots()->where('is_required', true)->orderBy('display_order');
    }

    public function optionalSlots(): HasMany
    {
        return $this->slots()->where('is_required', false)->orderBy('display_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'product_template_id');
    }

    /**
     * Convention 3: Check if this template is referenced by any products.
     */
    public function isReferencedByProducts(): bool
    {
        return $this->products()->exists();
    }
}
