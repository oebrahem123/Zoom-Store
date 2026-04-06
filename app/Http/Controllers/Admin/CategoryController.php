<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DeleteLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        // دي أهم سطر — لازم نجيب البيانات من قاعدة البيانات
        $categories = Category::withCount('products')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.Categories.create');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // تأكد من اسم الحقل 'image'
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->description = $request->description;

        // ✅ معالجة الصورة - بنفس طريقة store
        if ($request->hasFile('image')) {
            // 1. (اختياري) حذف الصورة القديمة إذا وجدت
            if ($category->imagepath && file_exists(public_path($category->imagepath))) {
                unlink(public_path($category->imagepath));
            }

            // 2. حفظ الصورة الجديدة بنفس طريقة الإضافة
            $fileName = time().'-'.$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/categories'), $fileName);
            $category->imagepath = 'uploads/categories/'.$fileName;
        }

        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'تم تعديل القسم بنجاح ✅');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'imagepath' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $category = new Category;
        $category->name = $request->name;
        $category->description = $request->description;

        if ($request->hasFile('imagepath')) {
            $fileName = time().'-'.$request->file('imagepath')->getClientOriginalName();
            $request->file('imagepath')->move(public_path('uploads/categories'), $fileName);
            $category->imagepath = 'uploads/categories/'.$fileName;
        }

        $category->save();

        return redirect()->route('admin.categories.index')
            ->with('success', 'تمت إضافة القسم بنجاح!');
    }

    public function destroy($id)
    {

        $category = Category::findOrFail($id);
        // ✅ التحقق اليدوي من تسجيل الدخول
        if (! Auth::check()) {
            return redirect()->route('login') // إعادة توجيه لصفحة تسجيل الدخول
                ->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $category = Category::findOrFail($id);
        $categoryName = $category->name;

        if ($category->products()->exists()) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف القسم "'.$categoryName.'" لأنه يحتوي على منتجات');
        }

        $category->delete();

        DeleteLog::create([
            'user_id' => Auth::id(),
            'action' => 'حذف قسم',
            'target' => $categoryName,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم حذف القسم "'.$categoryName.'" بنجاح');
    }
}
