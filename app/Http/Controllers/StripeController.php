<?php

namespace App\Http\Controllers;

use App\Jobs\SendCodeJob;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StripeController extends Controller
{
  public function redirect($data, $cartItems, $user, $currency)
  {
    Stripe::setApiKey(env('STRIPE_SECRET'));

    if (!$cartItems)
    {
      return response()->json(['error' => 'Cart is empty'], 400);
    }

    $lineItems = [];
    foreach ($cartItems as $cart)
    {
      $product = Product::find($cart->product_id);
      $price = $product->prices->$currency;
      $lineItems[] = [
        'price_data' => [
          'currency' => $currency,
          'unit_amount' => $price * 100,
          'product_data' => [
            'name' => $product->title_en,
          ],
        ],
        'quantity' => 1,
      ];
    }

    $session = Session::create([
      'payment_method_types' => [$data['method']],
      'customer_email' => $user->email,
      'line_items' => $lineItems,
      'mode' => 'payment',
      'success_url' => route('checkout.stripe_success', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
      'cancel_url' => route('checkout.stripe_cancel', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
    ]);

    foreach ($cartItems as $cart)
    {
      Order::create([
        'order_id' => uniqid(),
        'currency' => $currency,
        'total_amount' => $price,
        'payment_method' => $data['method'],
        'payment_status' => 'pending',
        'data' => $cart->data,
        'product_id' => $cart->product_id,
        'user_id' => $user->id,
        'session_id' => $session->id
      ]);
      $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
      $product = Product::find($cart->product_id);

      Notification::send($admins, new NewOrderNotification($product, $currency . $price, $user));
    }

    return response()->json([
      'url' => $session->url
    ]);
  }

  public function success(Request $request)
  {
    $stripe = new StripeClient(env('STRIPE_SECRET'));
    $sessionId = $request->get('session_id');

    try
    {
      $session = $stripe->checkout->sessions->retrieve($sessionId);
      $orders = Order::where('session_id', $session->id)->get();

      if (!$session)
      {
        throw new NotFoundHttpException();
      }

      $automaticProductsDetails = [];
      foreach ($orders as $order)
      {
        $order->update(['payment_status' => "complete"]);
        $order->product()->update(['sales_count' => $order->product()->sales_count + 1]);
        $product = $order->product;
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

      $user = $orders->first()->user();

      if (!empty($automaticProductsDetails))
      {
        SendCodeJob::dispatch($user, $automaticProductsDetails);
      }

      $cartIds = $orders->pluck('user_id');
      Cart::whereIn('user_id', $cartIds)->delete();

      return redirect()->away(env('FRONT_URL') . '/checkout/success');
    }
    catch (\Exception $e)
    {
      foreach ($orders as $order)
      {
        $order->update(['payment_status' => "cancel"]);
      }
      return redirect()->away(env('FRONT_URL'));
    }
  }

  public function cancel(Request $request)
  {
    $sessionId = $request->get('session_id');
    $orders = Order::where('session_id', $sessionId)->get();
    foreach ($orders as $order)
    {
      $order->update(['payment_status' => "cancel"]);
    }
    return redirect()->away(env('FRONT_URL'));
  }
}
