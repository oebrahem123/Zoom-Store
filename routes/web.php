<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DeleteLogController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FirstController;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// مجموعة الأدمن
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index')->middleware('auth');
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products/store', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::post('/products/{id}/update', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    // صفحة أضافة أكتر من صورة للمنتج
    // إضاقه أكثر من صورة
    Route::get('/AddProductImages/{Productid}', [ProductController::class, 'AddProductImages'])->name('admin.products.AddProductImages');
    // حذف الصورة
    Route::delete('/removeproductphoto/{imageid}', [ProductController::class, 'Removeproductphoto'])
        ->name('removeproductphoto');
    Route::post('/storeProductImage', [ProductController::class, 'storeProductImage'])->name('storeProductImage');
    // الأقسام
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/categories/store', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    // صفحه مين الحذف القسم
    Route::get('/delete-logs', [DeleteLogController::class, 'index'])->name('admin.delete_logs.index');
    // صفحه الأوردات
    Route::get('/previousorder', [CartController::class, 'previousorder'])->name('admin.orders.previousorder');

});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// الصفحة الرئيسية - اختر واحدة فقط
Route::get('/', [FirstController::class, 'index']); // ← هذه للصفحة الرئيسية

// صفحة المنتجات
Route::get('/product/{catid?}', [FirstController::class, 'GetCategoryProducts'])->name('prods');
// صفحه عرض المنتج بالصور
Route::get('/single-product/{productid}', [ProductController::class, 'showProduct'])
    ->name('product.details');

// صفحة عرض كل الأقسام بالمنتجات
Route::get('/category', [FirstController::class, 'GetAllGetCategoryWithProducts'])->name('cats');

// تخزين ريفيو جديد
Route::post('/storeReview', [FirstController::class, 'storeReview'])->name('storeReview');
// صفحة أراء العملاء
Route::get('/reviews', [FirstController::class, 'reviews'])->name('reviews');

Route::get('/single-product/{id}', [FirstController::class, 'showProduct']);
// البحث عن منتج
Route::post('/search', function (Request $request) {
    $products = Product::where('name', 'like', '%'.$request->searchkey.'%')->paginate(8);
    $categories = Category::all();

    return view('product', compact('products', 'categories'));
});
// صفحه الشراء cart
// تحديث منتج في السلة (للتعديل)
Route::post('/cart/update/{cartId}', [CartController::class, 'updateCartItem'])
    ->middleware('auth')
    ->name('cart.update');
// صفحة عرض السلة

Route::get('/cart', [CartController::class, 'cart'])
    ->middleware('auth')
    ->name('cart');
// إضافة منتج للسلة
Route::post('/addproducttocart/{productid}', [CartController::class, 'addProductToCart'])
    ->middleware('auth')
    ->name('cart.add');

// حذف منتج من السلة
Route::delete('/cart/{cartid}', [CartController::class, 'removeItem'])
    ->middleware('auth')
    ->name('cart.delete');

Route::get('/Completeorder', [CartController::class, 'Completeorder'])
    ->middleware('auth')
    ->name('Completeorder');
Route::post('/StoreOrder', [CartController::class, 'StoreOrder']);

Route::get('/order/confirmation/{id}', [CartController::class, 'orderConfirmation'])->name('order.confirmation');
