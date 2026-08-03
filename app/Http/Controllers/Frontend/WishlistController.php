<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $wishlistProducts = Wishlist::with('product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->pluck('product');

        return view('wishlist.index', compact('wishlistProducts'));
    }

    public function add(Request $request, $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'المنتج غير موجود'], 404);
            }
            return back()->with('error', 'المنتج غير موجود');
        }

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if (!$existing) {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
            ]);
        }

        $count = Wishlist::where('user_id', Auth::id())->count();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => 'تم إضافة المنتج إلى المفضلة',
                'wishlistCount' => $count,
                'added' => true,
            ]);
        }

        return back()->with('success', 'تم إضافة المنتج إلى المفضلة');
    }

    public function remove(Request $request, $productId)
    {
        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->delete();

        $count = Wishlist::where('user_id', Auth::id())->count();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => 'تم إزالة المنتج من المفضلة',
                'wishlistCount' => $count,
                'added' => false,
            ]);
        }

        return back()->with('success', 'تم إزالة المنتج من المفضلة');
    }
}
