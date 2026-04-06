<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function productphotos()
    {
        return $this->hasMany(ProductPoto::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
