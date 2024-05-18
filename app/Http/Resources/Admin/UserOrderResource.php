<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserOrderResource extends JsonResource
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
      'slug' => $this->product->slug,
      'image' => asset($this->product->images[0]->image),
      'payment_method' => $this->payment_method,
      'payment_status' => $this->payment_status,
      'amount' => $this->total_amount,
      'currency' => $this->currency,
      'title' => $this->product->title_en,
    ];
  }
}
