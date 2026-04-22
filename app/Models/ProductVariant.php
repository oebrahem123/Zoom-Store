<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class);

    }

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'quantity',
        'material',
        'weight',
    ];

    // ✅ تحديث الكمية الإجمالية للمنتج تلقائياً عند تغيير أي variant
    protected static function booted()
    {
        static::saved(function ($variant) {
            $total = $variant->product->variants()->sum('quantity');
            $variant->product->update(['quantity' => $total]);
        });

        static::deleted(function ($variant) {
            $total = $variant->product->variants()->sum('quantity');
            $variant->product->update(['quantity' => $total]);
        });
    }
}
