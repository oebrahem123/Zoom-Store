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
        'product_name',
        'product_price',
        'product_image',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * يحدد حالة التوفر للعرض في السلة والهيدر (منتج محذوف / نفد المخزون / متاح).
     */
    public function enrichAvailabilityAttributes(): self
    {
        $this->display_name = $this->product_name ?? $this->product?->name ?? 'منتج محذوف';
        // 🎨 محاولة جلب صورة حسب اللون
        $variantImage = null;

        if ($this->product && $this->product->relationLoaded('productphotos')) {
            $variantImage = $this->product->productphotos
                ->where('color', $this->color)
                ->first();
        }

        // 🧠 ترتيب الأولويات
        $this->display_image =
            $variantImage?->imagepath
            ?? $this->product_image
            ?? $this->product?->imagepath
            ?? 'images/default.png';
        $price = $this->product_price ?? $this->product?->price;
        $this->display_price = $price !== null ? (float) $price : 0.0;

        $productExists = $this->product !== null;
        $variantExists = $this->variant !== null;

        if (! $productExists) {
            $this->availabilityStatus = 'product_deleted';
            $this->availabilityMessage = 'تم حذف المنتج';
        } elseif (! $variantExists) {
            $this->availabilityStatus = 'variant_unavailable';
            $this->availabilityMessage = 'تم حذف المنتج أو غير متوفر';
        } elseif ($this->variant->quantity <= 0) {
            $this->availabilityStatus = 'out_of_stock';
            $this->availabilityMessage = 'المنتج لم يعد متاح حالياً';
        } elseif ($this->variant->quantity < $this->quantity) {
            $this->availabilityStatus = 'out_of_stock';
            $this->availabilityMessage = 'المنتج لم يعد متاح حالياً';
        } else {
            $this->availabilityStatus = 'available';
            $this->availabilityMessage = null;
        }

        $this->isAvailable = $this->availabilityStatus === 'available';

        return $this;
    }
}
