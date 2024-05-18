<?php

namespace App\Http\Controllers;

use App\Jobs\SendCodeJob;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class ChargilyController extends Controller
{
  public function redirect($data, $cartItems, $user)
  {
    if (!$cartItems)
    {
      return response()->json(['error' => 'Cart is empty'], 400);
    }

    $totalAmount = 0;
    foreach ($cartItems as $cart)
    {
      $product = Product::find($cart->product_id);
      $price = $product->prices->dzd;
      $totalAmount += $price;
    }

    $checkout = $this->chargilyPayInstance()->checkouts()->create([
      "metadata" => [],
      "locale" => "ar",
      "amount" => $totalAmount,
      "currency" => "dzd",
      "description" => "Payment ID=1",
      "success_url" => route("chargilypay.back"),
      "failure_url" => route("chargilypay.back"),
      "webhook_endpoint" => route("chargilypay.webhook_endpoint"),
    ]);

    foreach ($cartItems as $cart)
    {
      $product = Product::find($cart->product_id);
      $price = $product->prices->dzd;
      Order::create([
        'order_id' => uniqid(),
        'currency' => 'dzd',
        'total_amount' => $price,
        'payment_method' => $data['method'],
        'payment_status' => 'pending',
        'data' => $cart->data,
        'product_id' => $cart->product_id,
        'user_id' => $user->id,
        'session_id' => $checkout->getId()
      ]);
    }

    return response()->json([
      'url' => $checkout->getUrl()
    ]);
  }

  public function webhook()
  {
    $webhook = $this->chargilyPayInstance()->webhook()->get();

    if ($webhook)
    {
      $checkout = $webhook->getData();
      if ($checkout and $checkout instanceof \Chargily\ChargilyPay\Elements\CheckoutElement)
      {
        if ($checkout)
        {
          $orders = Order::where('session_id', $checkout->getId())->get();

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
      }
    }

    return redirect()->away(env('FRONT_URL'));
  }

  public function back(Request $request)
  {
    $checkout_id = $request->input("checkout_id");
    $checkout = $this->chargilyPayInstance()->checkouts()->get($checkout_id);
    if ($checkout)
    {
      $orders = Order::where('session_id', $checkout->getId())->get();
      if ($orders->isNotEmpty() && $orders->first()->payment_status === 'complete')
      {
        return redirect()->away(env('FRONT_URL') . '/checkout/success');
      }
      return redirect()->away(env('FRONT_URL'));
    }
    return redirect()->away(env('FRONT_URL'));
  }


  protected function chargilyPayInstance()
  {
    return new \Chargily\ChargilyPay\ChargilyPay(new \Chargily\ChargilyPay\Auth\Credentials([
      "mode" => "test",
      "public" => env('CHARGILY_PUBLIC_KEY'),
      "secret" => env('CHARGILY_SECRET_KEY'),
    ]));
  }
}
