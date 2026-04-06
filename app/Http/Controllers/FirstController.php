<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FirstController extends Controller
{
    // الصفحة الرئيسية (تجيب أقسام المنتجات فقط)
    public function MainPage()
    {
        $categories = Category::all();

        return view('welcome', ['categories' => $categories]);
    }

    // صفحة عرض جميع آراء العملاء
    public function reviews()
    {
        $reviews = Review::all();

        return view('reviews', ['reviews' => $reviews]);
    }

    // نسخة مطورة من الصفحة الرئيسية (تجيب الأقسام + آخر 6 آراء عملاء)
    public function index()
    {
        $categories = Category::all();
        $order = null;
        $reviews = Review::orderBy('created_at', 'desc')->take(6)->get();
        $allReviews = Review::orderBy('created_at', 'desc')->get();
        if (Auth::check()) {
            $order = Order::where('user_id', Auth::id())->latest()->first();
        }

        return view('welcome', compact('categories', 'reviews', 'order', 'allReviews'));
    }

    // حفظ رأي عميل جديد
    public function storeReview(Request $request)
    {
        if (! Auth::check()) {

            // حفظ البيانات مؤقتًا
            session([
                'review_data' => $request->only('name', 'email', 'message', 'rating', 'product_id'),
            ]);

            return redirect()->route('login')->with('error', ' يرجى تسجيل الدخول أولاً ');
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'message' => 'required',
            'product_id' => 'required',
        ]);

        Review::create([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'rating' => $request->rating ?? 5,
        ]);

        session()->forget('review_data');

        return back()->with('success', 'تم نشر التقييم بنجاح');
    }

    public function allReviews()
    {
        // استخدم paginate(10) وليس get()
        $reviews = Review::with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);  // مهم جداً: paginate وليس get

        return view('all-reviews', compact('reviews'));
    }

    public function GetCategoryProducts($catid = null)
    {
        if ($catid) {
            $products = Product::where('category_id', $catid)->paginate(6);
        } else {
            $products = Product::paginate(6);
        }

        return view('product', compact('products'));
    }

    public function GetAllGetCategoryWithProducts()
    {
        $categories = Category::all();
        $products = Product::all();

        return view('category', ['products' => $products, 'categories' => $categories]);
    }

    public function create()
    {
        $allcategories = Category::all();

        return view('admin.products.addproduct', ['allcategories' => $allcategories]);

    }

    // صفحه عرض صور المنتج

}
