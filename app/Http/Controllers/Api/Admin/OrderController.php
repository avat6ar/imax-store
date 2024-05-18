<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderSendRequest;
use App\Http\Resources\Admin\OrderResource;
use App\Jobs\SendOrderJob;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
  public function index(Request $request)
  {
    $query = Order::query();
    $searchQuery = $request->input('search');

    if (!empty($searchQuery))
    {
      $query->where(function ($query) use ($searchQuery)
      {
        $query->where('orders_id', 'like', '%' . $searchQuery . '%')
          ->orWhereHas('user', function ($query) use ($searchQuery)
          {
            $query->where('email', 'like', '%' . $searchQuery . '%')->orWhere('name', 'like', '%' . $searchQuery . '%');
          })
          ->orWhereHas('product', function ($query) use ($searchQuery)
          {
            $query->where('title_en', 'like', '%' . $searchQuery . '%');
          });
      });
    }

    $orders = $query->paginate(10);
    $ordersResource = OrderResource::collection($orders);

    return response()->json(['message' => 'Orders fetched successfully', 'products' => $ordersResource]);
  }

  public function request(Request $request)
  {
    $query = Order::where('payment_status', 'complete')->where('sending', false);
    $searchQuery = $request->input('search');

    if (!empty($searchQuery))
    {
      $query->where(function ($query) use ($searchQuery)
      {
        $query->where('orders_id', 'like', '%' . $searchQuery . '%')
          ->orWhereHas('user', function ($query) use ($searchQuery)
          {
            $query->where('email', 'like', '%' . $searchQuery . '%')->orWhere('name', 'like', '%' . $searchQuery . '%');
          })
          ->orWhereHas('product', function ($query) use ($searchQuery)
          {
            $query->where('title_en', 'like', '%' . $searchQuery . '%');
          });
      });
    }

    $orders = $query->paginate(10);
    $ordersResource = OrderResource::collection($orders);

    return response()->json(['message' => 'Orders fetched successfully', 'products' => $ordersResource]);
  }

  public function show($id)
  {
    $order = Order::where('order_id', $id)->first();

    if (!$order)
    {
      return response()->json(['message' => 'Order not found'], 404);
    }

    $orderResource = new OrderResource($order);
    return response()->json(['message' => 'Order fetched successfully', 'order' => $orderResource]);
  }

  public function sendOrder(OrderSendRequest $request, string $id)
  {
    $data = $request->validated();
    $admin = auth()->user();

    $order = Order::find($id);
    $user = $order->user()->first();

    if (!$order)
    {
      return response()->json(['message' => 'Order not found'], 404);
    }

    $message = strval($data['message']);

    $order->update([
      'message' => $data['message'],
      'sending' => true,
      'send_date' => now(),
      'send_admin' => $admin->email,
    ]);

    SendOrderJob::dispatch($order, $message, $user);

    return response()->json(['message' => 'Order sent successfully']);
  }
}
