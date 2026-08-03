<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\ShipmentGroup;
use App\Models\Wishlist;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (config('app.env') === 'production' && config('app.debug')) {
            throw new \RuntimeException(
                'Refusing to boot: APP_DEBUG is enabled while APP_ENV is set to production. '
                . 'Set APP_DEBUG=false in your environment file before deploying. '
                . 'Debug mode must never be enabled in production because it exposes '
                . 'configuration values, secrets, and internal error details.'
            );
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::if('permission', function (string $permission) {
            return app(PermissionService::class)->hasPermission($permission);
        });

        Blade::if('anypermission', function (array $permissions) {
            return app(PermissionService::class)->hasAnyPermission($permissions);
        });

        Blade::if('allpermissions', function (array $permissions) {
            return app(PermissionService::class)->hasAllPermissions($permissions);
        });
        view()->composer('*', function ($view) {
            $viewName = $view->getName();
            $startTime = microtime(true);

            $cartCount = 0;
            $headerCartItems = collect();
            $headerCartTotal = 0;
            $categories = collect();
            if (Schema::hasTable('categories')) {
                $categories = Category::select('id', 'name', 'description', 'imagepath')
                    ->withCount('products')
                    ->get();
            }
            $queries = 1; // categories

            if (Auth::check()) {
                $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
                $queries++;

                $headerCartItems = Cart::with(['product.productphotos', 'variant'])
                    ->where('user_id', Auth::id())
                    ->get()
                    ->map(fn ($item) => $item->enrichAvailabilityAttributes());
                $queries++;

                $headerCartTotal = $headerCartItems->sum(fn ($i) => $i->display_price * $i->quantity);
            }

            $hasTrackableShipment = false;
            if (Auth::check()) {
                $hasTrackableShipment = \App\Services\Workflow\ShipmentWorkflowPolicy::hasActiveShipments(Auth::user());
                $queries++;
            }

            $wishlistCount = 0;
            $wishlistProductIds = [];
            if (Auth::check()) {
                $wishlistCount = Wishlist::where('user_id', Auth::id())->count();
                $queries++;
                $wishlistProductIds = Wishlist::where('user_id', Auth::id())
                    ->pluck('product_id')
                    ->toArray();
                $queries++;
            }

            $elapsed = (microtime(true) - $startTime) * 1000;
            \Log::debug('[AUDIT_VIEW_COMPOSER]', [
                'view' => $viewName,
                'queries' => $queries,
                'elapsed_ms' => round($elapsed, 1),
                'authenticated' => Auth::check(),
                'cart_count' => $cartCount,
                'categories_count' => $categories->count(),
                'wishlist_count' => $wishlistCount,
                'has_shipments' => $hasTrackableShipment,
            ]);

            $view->with([
                'cartCount' => $cartCount,
                'headerCartItems' => $headerCartItems,
                'headerCartTotal' => $headerCartTotal,
                'categories' => $categories,
                'hasTrackableShipment' => $hasTrackableShipment,
                'wishlistCount' => $wishlistCount,
                'wishlistProductIds' => $wishlistProductIds,
            ]);
        });
    }
}
