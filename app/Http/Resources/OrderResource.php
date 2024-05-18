<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'payment_method' => $this->payment_method,
      'order_id' => $this->order_id,
      'payment_status' => $this->payment_status,
      'total_amount' => $this->total_amount,
      'currency' => $this->currency,
      'date' => date('j F Y g:i A', strtotime($this->created_at))
    ];
  }
}
