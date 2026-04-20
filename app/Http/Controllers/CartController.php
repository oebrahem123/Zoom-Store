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

        if ($cartItem->quantity > 1) {
            // 👇 ينقص واحد بس
            $cartItem->decrement('quantity');
        } else {
            // 👇 لو آخر واحد يتمسح
            $cartItem->delete();
        }

        return redirect()->route('cart')->with('success', 'تم تحديث السلة ✅');
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

    // تحديث منتج موجود في السلة (مقاس/لون مختلف)
    // public function updateCartItem(Request $request, $cartId)
    // {
    //     $user_id = auth()->id();

    //     // البحث عن العنصر الحالي في السلة
    //     $oldCartItem = Cart::where('id', $cartId)
    //         ->where('user_id', $user_id)
    //         ->firstOrFail();

    //     $request->validate([
    //         'variant_id' => 'required|exists:product_variants,id',
    //     ]);

    //     $newVariantId = $request->variant_id;
    //     $newVariant = ProductVariant::findOrFail($newVariantId);

    //     // التحقق من الكمية المتوفرة
    //     if ($newVariant->quantity <= 0) {
    //         return back()->with('error', '❌ هذا المزيج من المقاس واللون غير متوفر حالياً');
    //     }

    //     // هل يوجد نفس المنتج بنفس الـ variant الجديد في السلة بالفعل؟
    //     $existingCartItem = Cart::where('user_id', $user_id)
    //         ->where('product_id', $oldCartItem->product_id)
    //         ->where('variant_id', $newVariantId)
    //         ->where('id', '!=', $cartId) // استثناء العنصر الحالي
    //         ->first();

    //     if ($existingCartItem) {
    //         // إذا وجد، ندمج الكمية ونحذف القديم
    //         $newQuantity = $existingCartItem->quantity + $oldCartItem->quantity;

    //         // التحقق من الكمية المتوفرة
    //         if ($newQuantity > $newVariant->quantity) {
    //             return back()->with('error', '❌ الكمية المطلوبة غير متوفرة لهذا المزيج');
    //         }

    //         $existingCartItem->quantity = $newQuantity;
    //         $existingCartItem->save();

    //         // حذف العنصر القديم
    //         $oldCartItem->delete();

    //         return redirect()->route('cart')->with('success', '✅ تم تحديث المنتج في السلة');
    //     } else {
    //         // إذا لم يوجد، نقوم بتحديث العنصر الحالي بالـ variant الجديد
    //         $oldCartItem->update([
    //             'variant_id' => $newVariantId,
    //             'size' => $newVariant->size,
    //             'color' => $newVariant->color,
    //             // نحتفظ بنفس الكمية أو يمكنك إعادة تعيينها إلى 1
    //             'quantity' => $oldCartItem->quantity,
    //         ]);

    //         return redirect()->route('cart')->with('success', '✅ تم تعديل المنتج بنجاح');
    //     }
    // }

    public function updateCartItem(Request $request, $cartId)
    {
        $user_id = auth()->id();

        $oldCartItem = Cart::where('id', $cartId)
            ->where('user_id', $user_id)
            ->firstOrFail();

        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
        ]);

        $newVariant = ProductVariant::findOrFail($request->variant_id);

        // ❗ لو الكمية = 1 → عادي update
        if ($oldCartItem->quantity == 1) {

            $oldCartItem->update([
                'variant_id' => $newVariant->id,
                'size' => $newVariant->size,
                'color' => $newVariant->color,
            ]);

        } else {

            // 🔥 1. نقلل الكمية من القديم
            $oldCartItem->decrement('quantity');

            // 🔥 2. نشوف هل فيه نفس الـ variant الجديد
            $existing = Cart::where('user_id', $user_id)
                ->where('product_id', $oldCartItem->product_id)
                ->where('variant_id', $newVariant->id)
                ->first();

            if ($existing) {
                // نزود عليه
                $existing->increment('quantity');
            } else {
                // نعمل item جديد
                Cart::create([
                    'user_id' => $user_id,
                    'product_id' => $oldCartItem->product_id,
                    'quantity' => 1,
                    'size' => $newVariant->size,
                    'color' => $newVariant->color,
                    'variant_id' => $newVariant->id,
                ]);
            }
        }

        return redirect()->route('cart')->with('success', 'تم تعديل عنصر واحد فقط ✅');
    }
}
