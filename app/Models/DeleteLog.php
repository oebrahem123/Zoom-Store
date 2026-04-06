<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeleteLog extends Model
{
 use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'target',
    ];

    // علاقة مع المستخدم (اختيارية)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
