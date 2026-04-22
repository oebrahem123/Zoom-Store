<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPoto;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // صفحة إضافة منتج
    public function showProduct($productid, Request $request) // <-- أضفنا Request $request هنا
    {
        $product = Product::with('category', 'productphotos', 'variants', 'reviews')->findOrFail($productid);

        // --- الحل الجديد: استقبال size و color من الرابط ---
        $selectedSize = $request->query('size'); // بيجيب قيمة size من URL
        $selectedColor = $request->query('color'); // بيجيب قيمة color من URL

        // متغير لتخزين الـ variant ID المطابق (لحالته في الـ select)
        $selectedVariantId = null;

        // لو فيه size و color تم إرسالهم من السلة
        if ($selectedSize && $selectedColor) {
            // البحث عن الـ variant المطابق
            $variant = $product->variants()
                ->where('size', $selectedSize)
                ->where('color', $selectedColor)
                ->first();

            if ($variant) {
                $selectedVariantId = $variant->id;
            }
        }
        // ---------------------------------------------

        $price = $product->price;
        $minPrice = $price * 0.8;
        $maxPrice = $price * 1.2;
        $relatedProducts = Product::where('category_id', $product->category_id)->where('id', '!=', $productid)
            ->whereBetween('price', [$minPrice, $maxPrice])
            ->inRandomOrder()
            ->limit(3)
            ->get();

        // تمرير المتغيرات الجديدة للـ View
        return view('products.showProduct', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'selectedSize' => $selectedSize,      // <-- جديد
            'selectedColor' => $selectedColor,    // <-- جديد
            'selectedVariantId' => $selectedVariantId, // <-- جديد
        ]);
    }

    // صفحه إضافه للمنتج أكثر من صورة
    public function AddProductImages($productid)
    {
        $product = product::find($productid);
        $productImages = ProductPoto::where('product_id', $productid)->get();

        return view('admin.Products.AddProductImages', ['product' => $product, 'productImages' => $productImages, 'productid' => $productid,

        ]);

    }

    // حذف لصورة
    public function Removeproductphoto($imageid)
    {

        if ($imageid != null) {
            $photo = ProductPoto::findOrFail($imageid);

            // حذف الصورة من المجلد إن وجدت
            if (file_exists(public_path($photo->imagepath))) {
                unlink(public_path($photo->imagepath));
            }
            $product_id = $photo->product_id;
            $photo->delete();

            return redirect()
                ->route('admin.products.AddProductImages', $product_id)
                ->with('success', '✅ تم حذف الصورة بنجاح');
        } else {
            abort(403, 'please enter image id in the route');
        }

    }

    public function create()
    {
        $allcategories = Category::all();

        return view('admin.products.addproduct', ['allcategories' => $allcategories]);

    }

    // أضافه الصورة لصفحه إضافه أكثر من صورة للمنتج
    public function storeProductImage(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'color' => 'nullable|string',
        ]);

        $photo = new ProductPoto;
        $photo->product_id = $request->product_id;
        $photo->color = $request->color;

        if ($request->has('photo')) {
            $path = $request->photo->move(
                'uploads',
                Str::uuid()->toString().'_'.$request->photo->getClientOriginalName()
            );
            $photo->imagepath = $path;
        }

        $photo->save();

        return redirect()->back()->with('success', 'تمت إضافة الصورة بنجاح ✅');
    }

    // الأنتهاء من إضافه الصورة لصفحه إضافه أكثر من صورة للمنتج

    // إضافه منتج جديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'category_id' => 'nullable|integer',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string',
            'variants.*.color' => 'required|string',
            'variants.*.quantity' => 'required|integer|min:0',
        ]);

        $product = new Product;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->category_id = $request->category_id;

        // ✅ مؤقتاً نحط 0، وبعدين هتتحسب تلقائياً
        $product->quantity = 0;

        // حفظ الصورة
        $fileName = Str::uuid()->toString().'-'.$request->file('photo')->getClientOriginalName();
        $request->file('photo')->move(public_path('uploads'), $fileName);
        $product->imagepath = 'uploads/'.$fileName;

        $product->save();

        // إضافة المتغيرات
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                if (! empty($variant['size']) && ! empty($variant['color'])) {
                    $product->variants()->create([
                        'size' => $variant['size'],
                        'color' => $variant['color'],
                        'quantity' => $variant['quantity'] ?? 0,
                        'material' => $variant['material'] ?? null,
                        'weight' => $variant['weight'] ?? null,
                    ]);
                }
            }
        }

        // ✅ بعد إضافة المتغيرات، نحسب الكمية الإجمالية
        $totalQuantity = $product->variants()->sum('quantity');
        $product->quantity = $totalQuantity;
        $product->saveQuietly(); // حفظ بدون تشغيل أحداث إضافية

        return redirect()->route('admin.products.index')->with('success', 'تمت إضافة المنتج بنجاح ✅');
    }

    // عرض كل المنتجات
    public function index()
    {

        $products = Product::with('category')->get();

        return view('admin.Products.showproduct', compact('products'));
    }

    // صفحة تعديل منتج
    public function edit($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        $allcategories = Category::all();

        return view('admin.products.editproduct', compact('product', 'allcategories'));
    }

    // تنفيذ تعديل المنتج
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'category_id' => 'nullable|integer',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string',
            'variants.*.color' => 'required|string',
            'variants.*.quantity' => 'required|integer|min:0',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'category_id' => $request->category_id,
            // ❌ لا نحدث quantity هنا، هتتحسب تلقائياً بعد تعديل المتغيرات
        ]);

        // الصورة
        if ($request->hasFile('photo')) {
            if ($product->imagepath && file_exists(public_path($product->imagepath))) {
                unlink(public_path($product->imagepath));
            }
            $fileName = Str::uuid()->toString().'-'.$request->file('photo')->getClientOriginalName();
            $request->file('photo')->move(public_path('uploads'), $fileName);
            $product->imagepath = 'uploads/'.$fileName;
            $product->save();
        }

        // التعامل مع المتغيرات
        if ($request->has('variants')) {
            $existingIds = $product->variants->pluck('id')->toArray();
            $requestIds = [];

            foreach ($request->variants as $variant) {
                if (isset($variant['id'])) {
                    $requestIds[] = $variant['id'];
                    $existing = ProductVariant::find($variant['id']);
                    if ($existing) {
                        $existing->update([
                            'size' => $variant['size'],
                            'color' => $variant['color'],
                            'quantity' => $variant['quantity'],
                            'material' => $variant['material'] ?? null,
                            'weight' => $variant['weight'] ?? null,
                        ]);
                    }
                } else {
                    $new = $product->variants()->create([
                        'size' => $variant['size'],
                        'color' => $variant['color'],
                        'quantity' => $variant['quantity'],
                        'material' => $variant['material'] ?? null,
                        'weight' => $variant['weight'] ?? null,
                    ]);
                    $requestIds[] = $new->id;
                }
            }

            $idsToDelete = array_diff($existingIds, $requestIds);
            ProductVariant::whereIn('id', $idsToDelete)->delete();
        }

        // ✅ إعادة حساب الكمية الإجمالية بعد كل التعديلات
        $totalQuantity = $product->variants()->sum('quantity');
        $product->quantity = $totalQuantity;
        $product->saveQuietly();

        return redirect()->route('admin.products.index')->with('success', 'تم التعديل بنجاح 🔥');
    }

    // حذف منتج
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // حذف الصورة
        if ($product->imagepath && file_exists(public_path($product->imagepath))) {
            unlink(public_path($product->imagepath));
        }

        // 🔥 حذف variants
        $product->variants()->delete();

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'تم حذف المنتج بنجاح ✅');
    }
}
