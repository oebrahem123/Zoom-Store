<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class orderdetails extends Model
{
    protected $table = 'orderdetails';

    public function product()
    {
        return $this->belongsTo(product::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'quantity',
        'size',
        'color',
        'variant_id',
        'product_name',
        'product_image',
    ];

    public function lineTotal(): float
    {
        return (float) $this->price * (int) $this->quantity;
    }

    public function displayName(): string
    {
        return $this->product?->name ?? $this->product_name ?? 'منتج محذوف';
    }

    public function displayImagePath(): ?string
    {
        if ($this->product && $this->product->relationLoaded('productphotos')) {
            $img = $this->product->productphotos
                ->where('color', $this->color)
                ->first();

            if ($img) {
                return $img->imagepath;
            }
        }

        // fallback
        return $this->product?->imagepath ?? $this->product_image;
    }

    /**
     * حالة العرض في الطلبات السابقة (بعد الشراء): هل المنتج ما زال معروضاً أم حُذف أو نفد.
     */
    public function catalogStatus(): string
    {
        if ($this->product === null) {
            return 'deleted';
        }
        if ($this->variant_id === null || $this->variant === null) {
            return 'variant_unavailable';
        }
        if ($this->variant->quantity <= 0) {
            return 'out_of_stock';
        }

        return 'ok';
    }

    public function catalogStatusMessage(): ?string
    {
        return match ($this->catalogStatus()) {
            'deleted' => 'هذا المنتج لم يعد موجوداً في المتجر',
            'variant_unavailable' => 'هذا المنتج غير متوفر بالمواصفات المعروضة',
            'out_of_stock' => 'المنتج لم يعد متاح حالياً',
            default => null,
        };
    }
}
