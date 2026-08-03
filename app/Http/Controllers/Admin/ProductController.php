<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPoto;
use App\Models\Cart;
use App\Services\ProductService;
use App\Services\ProductTemplateService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private ProductTemplateService $templateService,
    ) {}

    // صفحة إضافة منتج
    public function showProduct($productid, Request $request)
    {
        $product = Product::with('category', 'productphotos', 'variants', 'reviews.user', 'printAreas')->findOrFail($productid);

        $selectedSize = trim($request->query('size'));
        $selectedColor = trim($request->query('color'));
        $selectedVariantId = null;

        if ($selectedSize && $selectedColor) {
            $variant = $product->variants()
                ->where('size', $selectedSize)
                ->where('color', $selectedColor)
                ->first();

            if ($variant) {
                $selectedVariantId = $variant->id;
            }
        }

        $price = $product->price;
        $minPrice = $price * 0.8;
        $maxPrice = $price * 1.2;
        $relatedProducts = Product::where('category_id', $product->category_id)->where('id', '!=', $productid)
            ->where('is_designable', $product->is_designable)
            ->whereBetween('price', [$minPrice, $maxPrice])
            ->inRandomOrder()
            ->limit(3)
            ->get();

        if ($relatedProducts->isEmpty()) {
            $relatedProducts = Product::where('category_id', $product->category_id)->where('id', '!=', $productid)
                ->where('is_designable', $product->is_designable)
                ->inRandomOrder()
                ->limit(3)
                ->get();
        }

        $initialQty = 1;
        $userCartItems = collect();

        if (auth()->check()) {
            if ($request->filled('cart_item_id')) {
                $cartItem = Cart::where('id', $request->cart_item_id)
                    ->where('user_id', auth()->id())
                    ->where('product_id', $productid)
                    ->first(['quantity']);
                if ($cartItem) {
                    $initialQty = $cartItem->quantity;
                }
            } elseif ($selectedVariantId) {
                $cartItem = Cart::where('user_id', auth()->id())
                    ->where('product_id', $productid)
                    ->where('variant_id', $selectedVariantId)
                    ->first(['quantity']);
                if ($cartItem) {
                    $initialQty = $cartItem->quantity;
                }
            }

            $userCartItems = Cart::where('user_id', auth()->id())
                ->where('product_id', $productid)
                ->get(['variant_id', 'quantity']);
        }

        return view('products.showProduct', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'selectedSize' => $selectedSize,
            'selectedColor' => $selectedColor,
            'selectedVariantId' => $selectedVariantId,
            'initialQty' => $initialQty,
            'userCartItems' => $userCartItems,
        ]);
    }

    public function AddProductImages($productid)
    {
        $product = Product::find($productid);
        $productImages = ProductPoto::where('product_id', $productid)->get();

        return view('admin.Products.AddProductImages', compact('product', 'productImages', 'productid'));
    }

    public function Removeproductphoto($imageid)
    {
        if ($imageid != null) {
            $photo = ProductPoto::findOrFail($imageid);
            $product_id = $photo->product_id;

            $this->productService->deleteProductPhoto($photo);

            return redirect()
                ->route('admin.products.edit', $product_id)
                ->with('success', '✅ تم حذف الصورة بنجاح');
        } else {
            abort(403, 'please enter image id in the route');
        }
    }

    public function createNormal()
    {
        return $this->showCreateForm('normal');
    }

    public function createCustom()
    {
        return $this->showCreateForm('custom');
    }

    private function showCreateForm(string $productType)
    {
        $allcategories = Category::all();
        $templates = $this->templateService->getTemplateChoices();

        return view('admin.products.addproduct', compact('allcategories', 'templates', 'productType'));
    }

    public function storeProductImage(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'color' => 'nullable|string',
            'view_name' => 'nullable|string',
        ]);

        $photo = new ProductPoto;
        $photo->product_id = $request->product_id;

        $this->productService->storeProductPhoto(
            $photo,
            $request->file('photo'),
            $request->color,
            $request->filled('view_name') ? $request->view_name : null,
        );

        return redirect()->back()->with('success', 'تمت إضافة الصورة بنجاح ✅');
    }

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
            'product_template_id' => 'nullable|integer|exists:product_templates,id',
        ]);

        $productType = $request->query('type', 'normal');

        $product = $this->productService->create(
            $request->only([
                'name', 'price', 'description', 'category_id',
                'is_designable', 'print_cost_type', 'product_template_id', 'variants',
            ]),
            $productType,
            $request->file('photo'),
        );

        return redirect()->route('admin.products.edit', $product->id)->with('success', 'تمت إضافة المنتج بنجاح');
    }

    public function index()
    {
        $products = Product::with('category', 'printAreas', 'variants')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.Products.showproduct', compact('products'));
    }

    public function edit($id)
    {
        $product = Product::with('variants', 'productphotos', 'printAreas', 'template')->findOrFail($id);
        $allcategories = Category::all();

        return view('admin.products.editproduct', compact('product', 'allcategories'));
    }

    public function update(Request $request, $id)
    {
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
            'type' => 'nullable|string|in:normal,custom',
            'product_template_id' => 'nullable|integer|exists:product_templates,id',
        ]);

        $product = Product::findOrFail($id);

        $this->productService->update(
            $product,
            $request->only([
                'name', 'price', 'description', 'category_id',
                'type', 'is_designable', 'print_cost_type', 'product_template_id', 'variants',
            ]),
            $request->hasFile('photo') ? $request->file('photo') : null,
        );

        return redirect()->route('admin.products.edit', $id)->with('success', 'تم التعديل بنجاح 🔥');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $this->productService->delete($product);

        return redirect()->route('admin.products.index')->with('success', 'تم حذف المنتج بنجاح ✅');
    }
}
