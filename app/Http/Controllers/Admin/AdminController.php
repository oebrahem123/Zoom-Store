<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Models\orderdetails;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
       public function index()
    {
        // 🧮 الإحصائيات الأساسية
        $productsCount = Product::count();
        $categoriesCount = Category::count();
        $ordersCount = Order::count();
        $usersCount = User::count();

        // 💰 إجمالي المبيعات و عدد المنتجات المباعة
        $totalSales = orderdetails::sum('price');
        $totalSoldProducts = orderdetails::sum('quantity');

        // 📊 رسم بياني لعدد الطلبات آخر 7 أيام
        $salesData = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $dates = $salesData->pluck('date');
        $counts = $salesData->pluck('count');

        // 🌍 تحديد الموقع (حسب الـ IP)
        $ip = request()->ip();

        if ($ip == '127.0.0.1' || $ip == '::1') {
            $city = 'Cairo';
            $country = 'Egypt';
        } else {
            try {
                $locationResponse = Http::timeout(5)->get("https://ipapi.co/{$ip}/json/");
                $city = $locationResponse->json('city', 'Cairo');
                $country = $locationResponse->json('country_name', 'Egypt');
            } catch (\Exception $e) {
                $city = 'Cairo';
                $country = 'Egypt';
            }
        }

        // ☀️ جلب بيانات الطقس من OpenWeather
        $apiKey = env('OPENWEATHER_API_KEY', '');
        $temperature = 30;
        $weatherDesc = 'غير محدد';
        $weatherIcon = '01d';

        if ($apiKey) {
            try {
                $weatherResponse = Http::timeout(5)->get("https://api.openweathermap.org/data/2.5/weather", [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => 'metric',
                    'lang' => 'ar',
                ]);

                if ($weatherResponse->successful()) {
                    $temperature = $weatherResponse->json('main.temp', 30);
                    $weatherDesc = $weatherResponse->json('weather.0.description', 'غير محدد');
                    $weatherIcon = $weatherResponse->json('weather.0.icon', '01d');
                }
            } catch (\Exception $e) {
                // في حال فشل الاتصال نستخدم القيم الافتراضية
            }
        }

        // ✅ تمرير كل البيانات إلى الواجهة
        return view('admin.index', compact(
            'productsCount',
            'categoriesCount',
            'ordersCount',
            'usersCount',
            'totalSales',
            'totalSoldProducts',
            'dates',
            'counts',
            'temperature',
            'city',
            'country',
            'weatherDesc',
            'weatherIcon'
        ));
    }







}
