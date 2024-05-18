<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;

class CartController extends Controller
{
  public function index()
  {
    $user = User::find(auth()->user()->id);

    $carts = $user->cart()->get();

    if (!$carts->count())
    {
      return response()->json(['message' => 'No carts found for the current user'], 404);
    }

    $cartResource = CartResource::collection($carts);

    $totalAmount = [
      'usd' => 0,
      'eur' => 0,
      'egp' => 0,
      'kwd' => 0,
      'sar' => 0,
      'aed' => 0,
      'dzd' => 0,
      'imx' => 0
    ];
    $totalAmountIXM = 0;
    foreach ($carts as $cart)
    {
      foreach ($cart->product->prices->toArray() as $currency => $price)
      {
        if (array_key_exists($currency, $totalAmount))
        {
          $totalAmount[$currency] += $price;
        }
        if ($currency == "imx")
        {
          $totalAmountIXM += $price;
        }
      }
    }

    return response()->json([
      'message' => 'Carts geted successfully',
      'cart' => $cartResource,
      'total_amount' => $totalAmount,
      'total_amount_imx' => $totalAmountIXM
    ]);
  }

  public function store(CartRequest $request)
  {
    $data = $request->validated();

    $existingItem = Cart::where('product_id', $data['product_id'])
      ->where('user_id', $data['user_id'])
      ->first();

    if ($existingItem)
    {
      return response()->json(['message' => 'Product already exists in cart'], 200);
    }
    $product = Product::find($data['product_id']);
    if ($product->availability == "out of stock")
    {
      return response()->json(['message' => 'Product is out of stock'], 404);
    }

    Cart::create($data);
    return response()->json(['message' => 'Product added to cart successfully'], 201);
  }

  public function destroy(string $id)
  {
    $user = User::find(auth()->user()->id);

    $cart = $user->cart()->find($id);

    if (!$cart)
    {
      return response()->json(['message' => 'Cart not found for the current user'], 404);
    }

    $cart->delete();
    return response()->json(['message' => 'Product removed from cart successfully'], 200);
  }
}
