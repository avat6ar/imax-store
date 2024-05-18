<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'orders' => $this['orders']->map(function ($order)
      {
        return [
          'id' => $order['id'],
          'date' => date('m-d', strtotime($order['created_at'])),
          'value' => $order['total_amount'] . $order['currency'],
        ];
      })->toArray(),
      'products' => $this['products']->map(function ($product)
      {
        return [
          'id' => $product['id'],
          'date' => date('m-d', strtotime($product['created_at'])),
          'value' => $product['prices']['usd'] . "$",
        ];
      })->toArray(),
    ];
  }
}
