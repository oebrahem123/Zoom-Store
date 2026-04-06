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
        if (Auth::check()) {
            $order = Order::where('user_id', Auth::id())->latest()->first();
        }

        return view('welcome', compact('categories', 'reviews', 'order'));
    }

    // حفظ رأي عميل جديد
    public function storeReview(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);
        $newReview = new Review;
        $newReview->name = $request->name;
        $newReview->email = $request->email;
        $newReview->phone = $request->phone;
        $newReview->subject = $request->subject;
        $newReview->message = $request->message;
        $newReview->save();

        // بعد الحفظ نعرض صفحة آراء العملاء
        $reviews = Review::all();

        return view('reviews', ['reviews' => $reviews]);
    }

    // صفحة المنتجات
    // جلب المنتجات الخاصة بقسم معين (أو جميع المنتجات لو مفيش قسم محدد)
    public function GetCategoryProducts($catid = null)
    {
        if ($catid) {
            $products = Product::where('category_id', $catid)->paginate(6);
        } else {
            $products = Product::paginate(6);
        }

        return view('product', compact('products'));
    }

    // إنتهاء صفحة المنتجات
    // عرض جميع أقسام المنتجات مع كل المنتجات
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
