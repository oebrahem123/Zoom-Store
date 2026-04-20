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

    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'quantity',
        'size',
        'color',
        'variant_id',
    ];
}
