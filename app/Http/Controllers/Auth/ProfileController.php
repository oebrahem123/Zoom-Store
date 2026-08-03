<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShipmentGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Load orders with shipment groups, newest first
        $orders = Order::with('shipmentGroup')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Group orders by shipment_group_id for display
        $shipmentGroups = ShipmentGroup::with('orders')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('auth.profile.index', compact(
            'user',
            'orders',
            'shipmentGroups',
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
        ]);

        auth()->user()->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'تم تحديث البيانات');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (! Hash::check(
            $request->current_password,
            auth()->user()->password
        )) {

            return back()->withErrors([
                'current_password' => 'كلمة المرور الحالية غير صحيحة',
            ]);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with(
            'success',
            'تم تغيير كلمة المرور'
        );
    }
}
