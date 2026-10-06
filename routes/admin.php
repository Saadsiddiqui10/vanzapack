<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\ProductImportController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShippingMethodController;
use App\Http\Controllers\Admin\TaxRateController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['web', 'auth', 'staff'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Catalog
        Route::middleware('permission:products.view')->group(function () {
            Route::get('products/import', [ProductImportController::class, 'form'])->name('products.import.form');
            Route::post('products/import', [ProductImportController::class, 'import'])->name('products.import');
            Route::get('products/import/template', [ProductImportController::class, 'template'])->name('products.import.template');
            Route::get('products/import/missing-photos', [ProductImportController::class, 'missingPhotos'])->name('products.import.missing-photos');
            Route::delete('products/bulk/destroy', [ProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
            Route::post('products/bulk/restore', [ProductController::class, 'bulkRestore'])->name('products.bulk-restore');
            Route::delete('products/bulk/force', [ProductController::class, 'bulkForceDestroy'])->name('products.bulk-force');
            Route::put('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
            Route::delete('products/{id}/force', [ProductController::class, 'forceDestroy'])->name('products.force-destroy');
            Route::resource('products', ProductController::class);
            Route::post('products/{product}/images', [ProductController::class, 'uploadImage'])->name('products.images.store');
            Route::delete('products/images/{image}', [ProductController::class, 'deleteImage'])->name('products.images.destroy');
            Route::post('products/images/reorder', [ProductController::class, 'reorderImages'])->name('products.images.reorder');
        });

        Route::middleware('permission:categories.view')->group(function () {
            Route::delete('categories/bulk/destroy', [CategoryController::class, 'bulkDestroy'])->name('categories.bulk-destroy');
            Route::post('categories/bulk/restore', [CategoryController::class, 'bulkRestore'])->name('categories.bulk-restore');
            Route::delete('categories/bulk/force', [CategoryController::class, 'bulkForceDestroy'])->name('categories.bulk-force');
            Route::put('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
            Route::delete('categories/{id}/force', [CategoryController::class, 'forceDestroy'])->name('categories.force-destroy');
            Route::resource('categories', CategoryController::class);
        });
        Route::resource('brands', BrandController::class)->middleware('permission:brands.view');
        Route::resource('attributes', AttributeController::class)->middleware('permission:attributes.view');

        // Inventory
        Route::middleware('permission:inventory.view')->group(function () {
            Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
            Route::put('inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
            Route::get('inventory/{inventory}/history', [InventoryController::class, 'history'])->name('inventory.history');
        });

        // Orders
        Route::middleware('permission:orders.view')->group(function () {
            Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
            Route::put('orders/{order}/details', [OrderController::class, 'updateDetails'])->name('orders.details');
            Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
            Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        });

        // Customers
        Route::middleware('permission:customers.view')->group(function () {
            Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::get('customers/{user}', [CustomerController::class, 'show'])->name('customers.show');
            Route::put('customers/{user}', [CustomerController::class, 'update'])->name('customers.update');
        });

        // Marketing
        Route::resource('coupons', CouponController::class)->middleware('permission:coupons.view');
        Route::resource('banners', BannerController::class)->middleware('permission:banners.view');
        Route::resource('pages', PageController::class)->middleware('permission:pages.view');
        Route::middleware('permission:banners.view')->group(function () {
            Route::resource('announcements', AnnouncementController::class)->except('show');
            Route::resource('menu-items', MenuItemController::class)->except('show')
                ->parameters(['menu-items' => 'menuItem']);
        });
        Route::middleware('permission:newsletter.view')->group(function () {
            Route::get('newsletter', [NewsletterController::class, 'index'])->name('newsletter.index');
            Route::delete('newsletter/{subscriber}', [NewsletterController::class, 'destroy'])->name('newsletter.destroy');
        });
        Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::put('messages/{message}', [ContactMessageController::class, 'update'])->name('messages.update');
        Route::delete('messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

        // Reviews
        Route::middleware('permission:reviews.view')->group(function () {
            Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
            Route::put('reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
            Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
        });

        // Sales / reports
        Route::middleware('permission:reports.view')->group(function () {
            Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('exports/{resource}', [ExportController::class, 'download'])->name('exports.download');
        });

        // Configuration
        Route::middleware('permission:shipping.view')->resource('shipping-methods', ShippingMethodController::class)->except('show');
        Route::middleware('permission:tax.view')->resource('tax-rates', TaxRateController::class)->except('show');
        Route::middleware('permission:settings.manage')->group(function () {
            Route::get('settings/{group?}', [SettingController::class, 'edit'])->name('settings.edit');
            Route::put('settings/{group}', [SettingController::class, 'update'])->name('settings.update');
        });

        // System
        Route::middleware('permission:users.manage')->group(function () {
            Route::resource('users', AdminUserController::class)->except('show');
            Route::resource('roles', RoleController::class)->except('show');
        });
        Route::get('activity', [ActivityLogController::class, 'index'])
            ->middleware('permission:activity.view')->name('activity.index');
    });
