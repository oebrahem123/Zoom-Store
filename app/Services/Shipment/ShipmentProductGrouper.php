<?php

namespace App\Services\Shipment;

use App\Models\ShipmentGroup;
use Illuminate\Support\Collection;

class ShipmentProductGrouper
{
    public static function group(ShipmentGroup $shipment): Collection
    {
        return $shipment->orders->map(fn($order) => [
            'order' => $order,
            'details' => $order->orderdetails,
        ]);
    }
}
