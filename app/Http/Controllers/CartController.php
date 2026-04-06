<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Session;

use App\Models\Cart;
use App\Models\Order;
use App\Models\orderdetails;
use App\Models\Product;
use Illuminate\Http\Request;

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

    public function addProductToCart($productid)
    {
        $user_id = auth()->id();

        // التحقق من أن المنتج موجود
        $product = Product::find($productid);
        if (! $product) {
            return redirect()->back()->with('error', '❌ المنتج غير موجود');
        }

        // البحث في السلة لو المنتج موجود بالفعل
        $cartItem = Cart::where('user_id', $user_id)
            ->where('product_id', $productid)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += 1;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => $user_id,
                'product_id' => $productid,
                'quantity' => 1,
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
       $result= Order::with('orderdetails')->get();


        return view('admin.orders.previousorder',['orders'=> $result]);
    }

   public function StoreOrder(Request $request)
{
    $user_id = auth()->user()->id;

    $newOrder = new Order();
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
