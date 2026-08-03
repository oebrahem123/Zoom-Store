<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\orderdetails;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShipmentGroup;
use App\Services\Shipment\ShipmentMergeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function cart()
    {
        $user_id = auth()->id();

        $cartProducts = Cart::with(['product.productphotos', 'variant'])
            ->where('user_id', $user_id)
            ->get()
            ->map(fn ($item) => $item->enrichAvailabilityAttributes());

        return view('products.cart', compact('cartProducts'));
    }

    public function addProductToCart(Request $request, $productid)
    {
        \Log::info('[CHECKPOINT-10] cart.add request', [
            'user_id' => auth()->id(),
            'product_id' => $productid,
            'variant_id' => $request->variant_id,
            'design_id' => $request->design_id,
            'cart_item_id' => $request->cart_item_id,
            'has_design_id' => $request->filled('design_id'),
            'session_all' => session()->all(),
            'referer' => request()->header('referer'),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);

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

        // ✅ نجيب الـ variant الأول (مهم جداً)
        $variant = ProductVariant::where('id', $variantId)
            ->where('product_id', $productid)
            ->first();

        if (! $variant) {
            return back()->with('error', '❌ هذا المزيج من المقاس واللون غير متوفر لهذا المنتج');
        }

        // ✅ التحقق من الكمية
        if ($variant->quantity <= 0) {
            return back()->with('error', '❌ هذا المزيج من المقاس واللون غير متوفر حالياً');
        }

        // =====================================================
        // 🟢 حالة التعديل (لو جاي من الكارت)
        // =====================================================
        if ($request->filled('cart_item_id')) {

            $cartItem = Cart::where('id', $request->cart_item_id)
                ->where('user_id', $user_id)
                ->first();

            if ($cartItem) {

                // 🧠 تعديل مباشر (بدون إضافة)
                $cartItem->variant_id = $variantId;
                $cartItem->size = $variant->size;
                $cartItem->color = $variant->color;

                // تحديث snapshot
                $cartItem->product_name = $product->name;
                $cartItem->product_price = $product->price;
                $cartItem->product_image = $product->imagepath;
                $cartItem->design_id = $request->design_id;

                $cartItem->save();

                return redirect()->route('cart')->with('success', '✅ تم تعديل المنتج');
            }
        }

        // =====================================================
        // 🟢 الإضافة العادية
        // =====================================================
        $quantity = max(1, (int) ($request->quantity ?? 1));

        $cartItem = Cart::where('user_id', $user_id)
            ->where('product_id', $productid)
            ->where('variant_id', $variantId)
            ->where('design_id', $request->design_id)
            ->first();

        if ($cartItem) {

            $newQty = $cartItem->quantity + $quantity;

            if ($newQty > $variant->quantity) {
                return back()->with('error', '❌ الكمية المطلوبة غير متوفرة لهذا المزيج');
            }

            $cartItem->quantity = $newQty;

            $cartItem->product_name = $product->name;
            $cartItem->product_price = $product->price;
            $cartItem->product_image = $product->imagepath;

            $cartItem->save();

            if (! $user_id) {
                session()->push('guest_cart_ids', $cartItem->id);
            }

        } else {

            if ($quantity > $variant->quantity) {
                return back()->with('error', '❌ الكمية المطلوبة غير متوفرة');
            }

            $newCart = Cart::create([
                'user_id' => $user_id,
                'product_id' => $productid,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'product_image' => $product->imagepath,
                'quantity' => $quantity,
                'size' => $variant->size,
                'color' => $variant->color,
                'variant_id' => $variantId,
                'design_id' => $request->design_id,
            ]);

            if (! $user_id) {
                session()->push('guest_cart_ids', $newCart->id);
            }
        }

        return redirect()->route('cart')->with('success', '✅ تم إضافة المنتج إلى السلة');
    }

    public function removeItem($cartid)
    {
        $cartItem = Cart::where('id', $cartid)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        if ($cartItem->quantity > 1) {
            // 👇 ينقص واحد بس
            $cartItem->decrement('quantity');
        } else {
            // 👇 لو آخر واحد يتمسح
            $cartItem->delete();
        }

        return redirect()->route('cart')->with('success', 'تم تحديث السلة ✅');
    }

    public function updateQuantity(Request $request, $cartId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cartItem = Cart::with('variant')
            ->where('id', $cartId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $newQty = (int) $request->quantity;

        if ($newQty <= 0) {
            $cartItem->delete();
            return redirect()->route('cart')->with('success', 'تمت إزالة المنتج من السلة ✅');
        }

        if ($cartItem->variant && $newQty > $cartItem->variant->quantity) {
            return redirect()->route('cart')->with('error', '❌ الكمية المطلوبة غير متوفرة');
        }

        $cartItem->quantity = $newQty;
        $cartItem->save();

        return redirect()->route('cart')->with('success', 'تم تحديث الكمية ✅');
    }

    public function Completeorder()
    {
        $user_id = auth()->id();
        $cartProducts = Cart::with(['product.productphotos', 'variant', 'design'])
            ->where('user_id', $user_id)
            ->get()
            ->map(fn ($item) => $item->enrichAvailabilityAttributes());

        $mergeService = app(ShipmentMergeService::class);
        $editableShipments = $mergeService->getEditableShipments(auth()->user());

        $shipmentShippingInfo = $editableShipments->mapWithKeys(fn($s) => [
            $s->id => [
                'name'    => $s->orders->last()?->name ?? '',
                'email'   => $s->orders->last()?->email ?? '',
                'address' => $s->orders->last()?->address ?? '',
                'phone'   => $s->orders->last()?->phone ?? '',
                'note'    => $s->orders->last()?->note ?? '',
            ]
        ]);

        return view('products.completeorder', compact(
            'cartProducts',
            'editableShipments',
            'shipmentShippingInfo',
        ));
    }

    // عرض صفحه عمليات الشراء
    public function previousorder()
    {
        return redirect()->route('admin.orders.previousorder');
    }

    public function StoreOrder(Request $request)
    {
        $user_id = auth()->user()->id;

        $cartProducts = Cart::with(['product.productphotos', 'variant', 'design'])
            ->where('user_id', $user_id)
            ->get()
            ->map(fn ($item) => $item->enrichAvailabilityAttributes());

        foreach ($cartProducts as $item) {
            if (! $item->isAvailable) {
                return redirect()->route('cart')->with('error', 'يوجد منتجات غير متاحة في السلة (محذوفة أو غير متوفرة حالياً). راجع السلة ثم أعد المحاولة.');
            }
        }

        return DB::transaction(function () use ($request, $user_id, $cartProducts) {

            // =============================================
            // 1. Create the new order
            // =============================================
            $newOrder = new Order;
            $newOrder->name = $request->name;
            $newOrder->email = $request->email;
            $newOrder->address = $request->address;
            $newOrder->phone = $request->phone;
            $newOrder->note = $request->note;
            $newOrder->user_id = $user_id;
            $newOrder->save();

            // =============================================
            // 2. Save order details (cart items)
            // =============================================
            foreach ($cartProducts as $item) {
                $unitPrice = $item->display_price;

                $newOrderDetail = new orderdetails;
                $newOrderDetail->product_id = $item->product_id;
                $newOrderDetail->price = (int) round($unitPrice);
                $newOrderDetail->quantity = $item->quantity;
                $newOrderDetail->order_id = $newOrder->id;
                $newOrderDetail->size = $item->size;
                $newOrderDetail->color = $item->color;
                $newOrderDetail->variant_id = $item->variant_id;
                $newOrderDetail->product_name = $item->display_name;
                $newOrderDetail->product_image = $item->product_image ?? $item->product?->imagepath;
                $newOrderDetail->design_id = $item->design_id;
                $newOrderDetail->save();

                $variant = ProductVariant::where('id', $item->variant_id)
                    ->lockForUpdate()
                    ->first();

                if (! $variant) {
                    throw new \Exception('المنتج غير موجود');
                }

                if ($variant->quantity < $item->quantity) {
                    throw new \Exception('الكمية غير كافية');
                }

                $variant->decrement('quantity', $item->quantity);
                $variant->save();

                $product = $item->product;
                if ($product) {
                    $totalQuantity = $product->variants()->sum('quantity');
                    $product->quantity = $totalQuantity;
                    $product->saveQuietly();
                }
            }

            // =============================================
            // 3. Set initial status based on design presence
            // =============================================
            $hasDesign = $newOrder->orderdetails->contains(fn($d) => !is_null($d->design_id));
            $initialStatus = $hasDesign ? 'pending_review' : 'pending';
            if ($newOrder->status !== $initialStatus) {
                \Log::debug('[AUDIT_ORDER_STATUS] CartController@StoreOrder', [
                    'order_id' => $newOrder->id,
                    'from' => $newOrder->status,
                    'to' => $initialStatus,
                    'controller' => 'CartController@StoreOrder',
                    'file' => 'CartController.php',
                    'has_canTransition_check' => false,
                    'has_design' => $hasDesign,
                ]);
                $newOrder->update(['status' => $initialStatus]);
            }

            \App\Services\Order\OrderTimelineService::log($newOrder, $initialStatus, null, 'إنشاء الطلب');

            // =============================================
            // 4. Shipment — merge or create
            // =============================================
            $mergeService = app(ShipmentMergeService::class);
            $mergeShipmentId = $request->integer('merge_shipment_id');

            if ($mergeShipmentId > 0) {
                $targetShipment = ShipmentGroup::find($mergeShipmentId);

                if ($targetShipment && $mergeService->validateRaceCondition($targetShipment)) {
                    $mergeService->executeMerge($targetShipment, $newOrder);
                } else {
                    $mergeService->createShipmentWithOrder($newOrder);
                    session()->flash('warning', 'تم إنشاء شحنة جديدة لأن الشحنة المختارة لم تعد تقبل إضافة طلبات.');
                }
            } else {
                $mergeService->createShipmentWithOrder($newOrder);
            }

            // =============================================
            // 5. Clear cart & redirect
            // =============================================
            Cart::where('user_id', $user_id)->delete();

            return redirect()->route('orders.index');
        });
    }

    public function orderConfirmation($id)
    {
        $order = Order::with([
            'shipmentGroup.orders.orderdetails.product',
            'shipmentGroup.orders.orderdetails.variant',
            'shipmentGroup.orders.orderdetails.design',
        ])->findOrFail($id);

        $viewData = compact('order');

        if ($order->shipmentGroup) {
            $shipment = $order->shipmentGroup;
            $service = app(\App\Services\Shipment\CustomerShipmentViewService::class);
            $prepared = $service->prepare($shipment);
            $viewData = array_merge($viewData, ['shipment' => $shipment], $prepared);
        }

        return view('products.order-confirmation', $viewData);
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
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string',
            'phone' => 'required',
        ]);

        $newVariant = ProductVariant::findOrFail($request->variant_id);
        if ($oldCartItem->quantity == 1) {
            $oldCartItem->update([
                'variant_id' => $newVariant->id,
                'size' => $newVariant->size,
                'color' => $newVariant->color,
            ]);

        } else {
            $oldCartItem->decrement('quantity');
            $existing = Cart::where('user_id', $user_id)
                ->where('product_id', $oldCartItem->product_id)
                ->where('variant_id', $newVariant->id)
                ->first();

            if ($existing) {
                $existing->increment('quantity');
            } else {
                $product = Product::find($oldCartItem->product_id);
                Cart::create([
                    'user_id' => $user_id,
                    'product_id' => $oldCartItem->product_id,
                    'quantity' => 1,
                    'size' => $newVariant->size,
                    'color' => $newVariant->color,
                    'variant_id' => $newVariant->id,
                    'product_name' => $product?->name,
                    'product_price' => $product?->price,
                    'product_image' => $product?->imagepath,
                ]);
            }
        }

        return redirect()->route('cart')->with('success', 'تم تعديل عنصر واحد فقط ✅');
    }
}
