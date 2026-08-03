<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPoto extends Model
{
    protected $fillable = [
        'product_id',
        'view_name',
        'color',
        'imagepath',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
