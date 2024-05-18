<?php

use App\Http\Controllers\Api\Admin\CodeController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\ProductCategoryController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\ReviewController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\StripeController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware('language')->group(function ()
{
  Route::middleware(['auth:api', 'verified'])->group(function ()
  {
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/checkout', [PaymentController::class, 'checkout']);
    Route::middleware(['admin'])->prefix('dashboard')->group(function ()
    {
      Route::get('/', [DashboardController::class, 'index']);
      Route::get('/admin', [DashboardController::class, 'admin']);
      Route::apiResource('products', ProductController::class);
      Route::apiResource('users', UserController::class);
      Route::apiResource('orders', AdminOrderController::class);
      Route::post('/orders/send/{id}', [AdminOrderController::class, 'sendOrder']);
      Route::get('/request/orders', [AdminOrderController::class, 'request']);
      Route::get('products/view/{slug}', [ProductController::class, 'showDetails']);
      Route::post('/reviews/publish/{id}', [ReviewController::class, 'togglePublish']);
      Route::get('/feedback', [ReviewController::class, 'index']);
      Route::post('/reviews/{id}', [ReviewController::class, 'destroy']);
      Route::apiResource('categories', ProductCategoryController::class);
      Route::get('codes', [CodeController::class, 'index']);
      Route::post('codes', [CodeController::class, 'store']);
      Route::delete('codes/{id}', [CodeController::class, 'destroy']);
    });
    Route::post('codes/redeem', [CodeController::class, 'redeem']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/reviews', [PublicController::class, 'storeReview']);
    Route::get('/wishlists', [WishlistController::class, 'index']);
    Route::post('/wishlists', [WishlistController::class, 'store']);
    Route::post('/wishlists/{id}', [WishlistController::class, 'destroy']);
  });

  Route::prefix('auth')->group(function ()
  {
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::get('/users', [AuthController::class, 'index']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verification', [AuthController::class, 'verification']);
    Route::post('/foroget-password', [AuthController::class, 'forogetPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/resend-code', [AuthController::class, 'resendCode']);
    Route::post('/resend-code-password', [AuthController::class, 'resendCodePassword']);
    Route::post('/google-login', [AuthController::class, 'loginGoogle']);
  });

  Route::get('/categories', [PublicController::class, 'categories']);
  Route::get('/new-products', [PublicController::class, 'newProducts']);
  Route::get('/featured-products', [PublicController::class, 'featuredProducts']);
  Route::get('/best-products', [PublicController::class, 'bestProducts']);
  Route::get('/other-products', [PublicController::class, 'otherProducts']);
  Route::get('/products', [PublicController::class, 'products']);
  Route::get('/feedback', [PublicController::class, 'feedback']);
  Route::get('/products/{slug}', [PublicController::class, 'productDetails']);
});
Route::get('/checkout/success_stripe', [StripeController::class, 'success'])->name('checkout.stripe_success');
Route::get('/checkout/cancel_stripe', [StripeController::class, 'cancel'])->name('checkout.stripe_cancel');
