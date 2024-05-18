<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
  public function index()
  {
    $user = User::find(auth()->user()->id);
    $orders = $user->orders()->get();

    if (!count($orders)) {
      return response()->json(['message' => 'orders is empty']);
    }

    $ordersResource = OrderResource::collection($orders);
    
    return response()->json(['message' => 'Orders found successfully', 'orders' => $ordersResource]);
  }
}
