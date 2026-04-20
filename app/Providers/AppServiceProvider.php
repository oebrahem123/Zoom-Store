<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {

            $order = null;
            $cartCount = 0;
            $categories = Category::select('id', 'name', 'description', 'imagepath')
                ->withCount('products')
                ->get();
            if (auth()->check()) {

                $order = Order::where('user_id', auth()->id())
                    ->latest()
                    ->first();

                $cartCount = Cart::where('user_id', auth()->id())
                    ->sum('quantity');
            }

            $view->with([
                'order' => $order,
                'cartCount' => $cartCount,
                'categories' => $categories,
            ]);
        });
    }
}
