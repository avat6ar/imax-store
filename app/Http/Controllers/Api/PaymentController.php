<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ChargilyController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\StripeController;
use App\Http\Requests\PaymentRequest;
use App\Jobs\SendCodeJob;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Notification;

class PaymentController extends Controller
{
  public function checkout(PaymentRequest $request)
  {
    $data = $request->validated();
    $user = User::find(auth()->user()->id);
    $cartItems = $user->cart()->get();

    switch ($data['method'])
    {
      case 'imx':
        return $this->handleCheckoutByImx($cartItems, $user);
      case 'chargily':
        return (new ChargilyController())->redirect($data, $cartItems, $user);
      default:
        return (new StripeController())->redirect($data, $cartItems, $user, $data['currency']);
    }
  }

  public function handleCheckoutByImx($cartItems, $user)
  {
    $totalPrice = $this->calculateTotalPrice($cartItems, 'imx');

    if ($user->balance < $totalPrice)
    {
      $this->cancelOrders($cartItems);
      return response()->json(['message' => 'Insufficient funds'], 400);
    }

    $this->processOrders($cartItems, $user, $totalPrice, 'imx');

    return response()->json(['message' => 'Payment Successful'], 200);
  }

  private function calculateTotalPrice($cartItems, $currency)
  {
    $totalPrice = 0;
    foreach ($cartItems as $cart)
    {
      $product = Product::find($cart->product_id);
      $price = $product->prices->$currency;
      $totalPrice += $price;
    }
    return $totalPrice;
  }

  private function processOrders($cartItems, $user, $totalPrice, $currency)
  {
    foreach ($cartItems as $cart)
    {
      $product = Product::find($cart->product_id);
      $price = $product->prices->$currency;
      Order::create([
        'order_id' => uniqid(),
        'currency' => $currency,
        'total_amount' => $price,
        'payment_method' => $currency,
        'payment_status' => 'pending',
        'data' => $cart->data,
        'product_id' => $cart->product_id,
        'user_id' => $user->id,
        'session_id' => null,
        'type' => $product->type
      ]);
    }

    $user->decrement('balance', $totalPrice);
    $user->save();
    $user->cart()->delete();
    $this->handleCheckoutOrder($cartItems, $user);
  }

  private function cancelOrders($cartItems)
  {
    foreach ($cartItems as $item)
    {
      $order = Order::where('product_id', $item->product_id)->where('user_id', $item->user_id)->first();
      $order->update(['payment_status' => "cancelled", 'sending' => false]);
    }
  }

  private function handleCheckoutOrder($cartItems, $user)
  {
    $automaticProductsDetails = [];
    foreach ($cartItems as $item)
    {
      $product = Product::find($item->product_id);
      $order = Order::where('product_id', $item->product_id)->where('user_id', $item->user_id)->first();
      if ($product->type == 'automatic')
      {
        $validCodes = $product->codes()->where('status', 'available')->where('expire_date', '>', now())->get();
        if ($validCodes->count() > 0)
        {
          $randomCode = $validCodes->random();
          $randomCode->update(['status' => 'used', 'email' => $order->user()->email]);
          $automaticProductsDetails[] = ['name' => $product->title_en, 'code' => $randomCode->code];
          $order->update(['payment_status' => "complete", 'sending' => true, 'code' => $randomCode->code]);
          $product->update(['sales_count' => $product->sales_count + 1]);
        }
        else
        {
          $order->update(['payment_status' => "complete", 'sending' => false]);
        }
      }
      else
      {
        $order->update(['payment_status' => "complete", 'sending' => false]);
      }

      $order->update(['type' => $product->type]);
    }

    if (!empty($automaticProductsDetails))
    {
      SendCodeJob::dispatch($user, $automaticProductsDetails);
    }
  }
}
