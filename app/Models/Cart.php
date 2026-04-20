<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public function product()
    {
        return $this->belongsTo(product::class, 'product_id');
    }

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'size',
        'color',
        'variant_id',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
