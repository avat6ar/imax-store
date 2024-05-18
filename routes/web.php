<?php

use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\ChargilyController;
use App\Models\Product;
use App\Models\ProductInput;
use Illuminate\Support\Facades\Route;
use Mgcodeur\CurrencyConverter\Facades\CurrencyConverter;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
  return "laravel";
});

Route::post('chargilypay/webhook', [ChargilyController::class, "webhook"])->name("chargilypay.webhook_endpoint");
Route::get('chargilypay/back', [ChargilyController::class, "back"])->name("chargilypay.back");
