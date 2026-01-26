<?php

use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\Front\WelcomeController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\CurrencyController;
use App\Http\Controllers\Front\Customer\ProfileController;
use App\Http\Controllers\Front\PaymentGateway\StripeController;
use App\Http\Controllers\Front\ReviewController;
use App\Http\Controllers\Front\SearchController;
use App\Http\Controllers\Front\ShopController;
use App\Http\Controllers\Front\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/shop', [ProductController::class, 'index'])->name('shop.index');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/api/product/{product:slug}/quick-view', [ProductController::class, 'getQuickView'])->name('products.quick-view');
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::post('/change-currency', [CurrencyController::class, 'changeCurrency'])->name('change.currency');

Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');
Route::post('/cart/update', [CartController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'removeFromCart'])->name('cart.remove');


Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.applyCoupon');
Route::post('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.removeCoupon');

Route::get('/products', [ShopController::class, 'index'])->name('shop.products');

Route::get('/search-suggestions', [SearchController::class, 'suggestions']);
Route::get('/search', [SearchController::class, 'searchResults']);

Route::get('/get-variant-price', [ProductController::class, 'getVariantPrice'])->name('product.variant.price');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Category page
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

Route::post('/product/review/store', [ReviewController::class, 'store'])->name('review.store');

Route::prefix('customer')->name('customer.')->group(function () {

    // Guest routes
    Route::middleware('guest:customer')->group(function () {
        // Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        // Route::post('login', [LoginController::class, 'login']);

        // Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        // Route::post('register', [RegisterController::class, 'register']);

        // Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        // Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

        // Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        // Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
    });

    // Authenticated routes
    Route::middleware('auth.customer')->group(function () {
        Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        // Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

        //  Customer Profile Routes
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });
});

Route::get('/stripe/checkout', [StripeController::class, 'checkout'])->name('stripe.checkout.process');
