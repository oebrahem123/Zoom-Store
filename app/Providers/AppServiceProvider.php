<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
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
            $headerCartItems = collect();
            $headerCartTotal = 0;
            $categories = Category::select('id', 'name', 'description', 'imagepath')
                ->withCount('products')
                ->get();
            if (Auth::check()) {

                $order = Order::where('user_id', Auth::id())
                    ->latest()
                    ->first();

                $cartCount = Cart::where('user_id', Auth::id())
                    ->sum('quantity');

                $headerCartItems = Cart::with(['product.productphotos', 'variant'])
                    ->where('user_id', Auth::id())
                    ->get()
                    ->map(fn ($item) => $item->enrichAvailabilityAttributes());
                $headerCartTotal = $headerCartItems->sum(fn ($i) => $i->display_price * $i->quantity);
            }

            $view->with([
                'order' => $order,
                'cartCount' => $cartCount,
                'headerCartItems' => $headerCartItems,
                'headerCartTotal' => $headerCartTotal,
                'categories' => $categories,
            ]);
        });
    }
}
