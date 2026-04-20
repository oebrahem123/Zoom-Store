<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\orderdetails;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function cart()
    {
        $user_id = auth()->id();
        $cartProducts = Cart::with('product')
            ->where('user_id', $user_id)
            ->get();

        return view('products.cart', compact('cartProducts'));
    }

    public function addProductToCart(Request $request, $productid)
    {
        $user_id = auth()->id();

        $product = Product::find($productid);
        if (! $product) {
            return back()->with('error', '❌ المنتج غير موجود');
        }

        // ✅ التحقق من وجود variant_id
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
        ], [
            'variant_id.required' => 'يرجى اختيار المقاس واللون قبل إضافة المنتج إلى السلة',
            'variant_id.exists' => 'المنتج الذي اخترته غير متوفر بهذه المواصفات',
        ]);

        $variantId = $request->variant_id;

        // التحقق من أن الـ variant يخص هذا المنتج
        $variant = ProductVariant::where('id', $variantId)
            ->where('product_id', $productid)
            ->first();

        if (! $variant) {
            return back()->with('error', '❌ هذا المزيج من المقاس واللون غير متوفر لهذا المنتج');
        }

        // التحقق من وجود كمية كافية
        if ($variant->quantity <= 0) {
            return back()->with('error', '❌ هذا المزيج من المقاس واللون غير متوفر حالياً');
        }

        // البحث بنفس الـ variant
        $cartItem = Cart::where('user_id', $user_id)
            ->where('product_id', $productid)
            ->where('variant_id', $variantId)
            ->first();

        if ($cartItem) {
            // التحقق من أن الكمية المطلوبة لا تتجاوز المتوفر
            if ($cartItem->quantity + 1 > $variant->quantity) {
                return back()->with('error', '❌ الكمية المطلوبة غير متوفرة لهذا المزيج');
            }
            $cartItem->quantity += 1;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => $user_id,
                'product_id' => $productid,
                'quantity' => 1,
                'size' => $variant->size,
                'color' => $variant->color,
                'variant_id' => $variantId,
            ]);
        }

        return redirect()->route('cart')->with('success', '✅ تم إضافة المنتج إلى السلة');
    }

    public function removeItem($cartid)
    {
        $cartItem = Cart::findOrFail($cartid);
        $cartItem->delete();

        return redirect()->route('cart')->with('success', 'تم حذف المنتج من السلة بنجاح ✅');
    }

    public function Completeorder()
    {
        $user_id = auth()->user()->id;
        $cartProducts = Cart::with('product')
            ->where('user_id', $user_id)
            ->get();

        return view('products.completeorder', ['cartProducts' => $cartProducts]);
    }

    // عرض صفحه عمليات الشراء
    public function previousorder()
    {
        $result = Order::with('orderdetails')->get();

        return view('admin.orders.previousorder', ['orders' => $result]);
    }

    public function StoreOrder(Request $request)
    {
        $user_id = auth()->user()->id;

        $newOrder = new Order;
        $newOrder->name = $request->name;
        $newOrder->email = $request->email;
        $newOrder->address = $request->address;
        $newOrder->phone = $request->phone;
        $newOrder->note = $request->note;
        $newOrder->user_id = $user_id;
        $newOrder->save();

        $cartProducts = Cart::with('product')->where('user_id', $user_id)->get();
        foreach ($cartProducts as $item) {
            $newOrderDetail = new orderdetails;

            $newOrderDetail->product_id = $item->product_id;
            $newOrderDetail->price = $item->product->price;
            $newOrderDetail->quantity = $item->quantity;
            $newOrderDetail->order_id = $newOrder->id;

            // 👇 أهم حاجة
            $newOrderDetail->size = $item->size;
            $newOrderDetail->color = $item->color;
            $newOrderDetail->variant_id = $item->variant_id;

            $newOrderDetail->save();
        }

        // مسح السلة
        Cart::where('user_id', $user_id)->delete();

        // ✅ تخزين order ID في session
        session(['last_order_id' => $newOrder->id]);

        return redirect()->route('order.confirmation', $newOrder->id);
    }

    public function orderConfirmation($id)
    {
        $order = Order::findOrFail($id);

        return view('products.order-confirmation', compact('order'));
    }
}
