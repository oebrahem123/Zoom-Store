<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentGroup extends Model
{
    protected $fillable = [
        'user_id', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'shipment_group_id');
    }

    public function openOrders()
    {
        return $this->orders()->whereNotIn('status', ['delivered', 'cancelled']);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
