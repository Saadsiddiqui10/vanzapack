<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront
|--------------------------------------------------------------------------
*/
Route::middleware('storefront')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/offers', [ShopController::class, 'offers'])->name('shop.offers');
    Route::get('/new-arrivals', [ShopController::class, 'newArrivals'])->name('shop.new');
    Route::get('/best-sellers', [ShopController::class, 'bestSellers'])->name('shop.best');
    Route::get('/brands', [ShopController::class, 'brands'])->name('brands.index');
    Route::get('/brand/{brand:slug}', [ShopController::class, 'brand'])->name('brands.show');
    Route::get('/category/{category:slug}', [ShopController::class, 'category'])->name('category.show');

    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');

    Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/product/{product:slug}/quick-view', [ProductController::class, 'quickView'])->name('product.quick-view');
    Route::get('/product/{product:slug}/variant', [ProductController::class, 'variant'])->name('product.variant');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::get('/cart/mini', [CartController::class, 'mini'])->name('cart.mini');
    Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{item}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product:slug}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{product:slug}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Contact + newsletter
    Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

    // Checkout
    Route::middleware('throttle:20,1')->group(function () {
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/checkout/{order:number}/confirmation', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
        Route::get('/checkout/{order:number}/pay/mock', [CheckoutController::class, 'mockOnline'])->name('checkout.online.mock');
        Route::post('/checkout/{order:number}/pay/mock', [CheckoutController::class, 'mockOnlineConfirm'])->name('checkout.online.mock.confirm');
    });

    // Breeze compatibility alias
    Route::get('/dashboard', fn () => auth()->user()?->isStaff()
        ? redirect('/admin')
        : redirect()->route('account.dashboard'))
        ->middleware('auth')->name('dashboard');

    // Customer account
    Route::middleware(['auth', 'verified'])->prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AccountController::class, 'editProfile'])->name('profile');
        Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [AccountController::class, 'updatePassword'])->name('password.update');

        Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
        Route::get('/orders/{order:number}', [AccountController::class, 'showOrder'])->name('orders.show');
        Route::post('/orders/{order:number}/cancel', [AccountController::class, 'cancelOrder'])->name('orders.cancel');
        Route::post('/orders/{order:number}/reorder', [AccountController::class, 'reorder'])->name('orders.reorder');

        Route::resource('addresses', AddressController::class)->except(['show', 'create', 'edit']);

        Route::get('/reviews', [ReviewController::class, 'mine'])->name('reviews');
        Route::post('/orders/{order:number}/items/{item}/review', [ReviewController::class, 'store'])->name('reviews.store');

        Route::get('/notifications', [AccountController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/read', [AccountController::class, 'readNotifications'])->name('notifications.read');
    });
});

/*
|--------------------------------------------------------------------------
| Uploaded media (symlink-independent)
|--------------------------------------------------------------------------
*/
Route::get('/media/{path}', \App\Http\Controllers\MediaController::class)
    ->where('path', '.*')
    ->name('media');

/*
|--------------------------------------------------------------------------
| SEO
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| CMS pages (keep last so it doesn't shadow named routes)
|--------------------------------------------------------------------------
*/
Route::middleware('storefront')->get('/page/{page:slug}', [PageController::class, 'show'])->name('page.show');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
