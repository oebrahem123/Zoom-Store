<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DeleteLogController;
use App\Http\Controllers\Admin\DesignController as AdminDesignController;
use App\Http\Controllers\Admin\OrderListingController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\PrintAreaController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ShipmentWorkflowController;
use App\Http\Controllers\Auth\Admin\AdminLoginController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {

    // guest admin routes (login)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminLoginController::class, 'login'])->name('admin.login.post');
    });

    // authenticated admin routes
    Route::middleware(['auth:admin', 'admin'])->group(function () {

        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

        Route::get('/', [AdminController::class, 'index'])->name('admin.index');

        // products
        Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/products/store', [ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
        Route::post('/products/{id}/update', [ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
        Route::get('/products/{product}/print-areas', [PrintAreaController::class, 'edit'])->name('admin.products.print-areas');
        Route::post('/products/{product}/print-areas/save', [PrintAreaController::class, 'save'])->name('admin.products.print-areas.save');
        Route::get('/AddProductImages/{productid}', [ProductController::class, 'AddProductImages'])->name('admin.products.AddProductImages');
        Route::delete('/removeproductphoto/{imageid}', [ProductController::class, 'Removeproductphoto'])->name('removeproductphoto');
        Route::post('/storeProductImage', [ProductController::class, 'storeProductImage'])->name('storeProductImage');

        // categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
        Route::post('/categories/store', [CategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

        // delete logs
        Route::get('/delete-logs', [DeleteLogController::class, 'index'])->name('admin.delete_logs.index');

        // orders
        Route::get('/previousorder', [OrderListingController::class, 'index'])->name('admin.orders.previousorder');
        Route::post('/shipments/{shipment}/workflow', ShipmentWorkflowController::class)->name('admin.shipments.workflow');
        Route::get('/orders/{orderId}/design/{detailId}', [AdminDesignController::class, 'show'])->name('admin.orders.design.show');
        Route::get('/orders/{orderId}/design/{detailId}/edit', [AdminDesignController::class, 'edit'])->name('admin.orders.design.edit');
        Route::post('/orders/{orderId}/design/{detailId}/reject', [AdminDesignController::class, 'reject'])->name('admin.orders.design.reject');
        Route::post('/orders/{orderId}/design/{detailId}/approve', [AdminDesignController::class, 'approve'])->name('admin.orders.design.approve');
        Route::post('/orders/{order}/status', [OrderStatusController::class, 'update'])->name('admin.orders.status.update');

        // audit logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit_logs.index');

        // admin management (super_admin only)
        Route::middleware('can:manage-admins')->prefix('admins')->name('admin.admins.')->group(function () {
            Route::get('/', [AdminManagementController::class, 'index'])->name('index');
            Route::get('/create', [AdminManagementController::class, 'create'])->name('create');
            Route::post('/', [AdminManagementController::class, 'store'])->name('store');
            Route::get('/{admin}', [AdminManagementController::class, 'show'])->name('show');
            Route::get('/{admin}/edit', [AdminManagementController::class, 'edit'])->name('edit');
            Route::put('/{admin}', [AdminManagementController::class, 'update'])->name('update');
            Route::delete('/{admin}', [AdminManagementController::class, 'destroy'])->name('destroy');
            Route::patch('/{admin}/toggle-active', [AdminManagementController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/{admin}/reset-password', [AdminManagementController::class, 'resetPassword'])->name('reset-password');
        });
    });
});
