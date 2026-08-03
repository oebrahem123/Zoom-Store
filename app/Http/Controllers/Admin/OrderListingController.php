<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderListingController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with([
            'orderdetails.product',
            'orderdetails.variant',
            'orderdetails.design.elements.printArea',
            'shipmentGroup.orders',
        ])->orderBy('created_at');

        if ($request->status === 'delivered') {
            $query->whereIn('status', ['delivered', 'cancelled']);
        } elseif ($request->status === 'active') {
            $query->whereNotIn('status', ['delivered', 'cancelled']);
        }

        $orders = $query->get();

        $grouped = $orders->groupBy(function ($o) {
            return $o->shipment_group_id ?? 'standalone';
        });

        $shipmentGroups = $grouped->filter(function ($v, $key) {
            return $key !== 'standalone';
        })->map(function ($orders) {
            return [
                'group'  => $orders->first()->shipmentGroup,
                'orders' => $orders,
            ];
        })->values();

        $standaloneOrders = $grouped->get('standalone', collect());

        return view('admin.orders.previousorder',
            compact('shipmentGroups', 'standaloneOrders'));
    }
}
