<?php

namespace App\Http\Resources\Admin;

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
      "id" => $this->id,
      "order_id" => $this->order_id,
      "username" => $this->user->name,
      "useremail" => $this->user->email,
      "title" => $this->product->title_en,
      "payment_status" => $this->payment_status,
      "payment_method" => $this->payment_method,
      "image" => asset($this->product->images[0]->image),
      "amount" => $this->total_amount,
      "currency" => $this->currency,
      "send" => $this->sending,
      "send_date" => $this->send_date ? date('d/m/Y', strtotime($this->send_date)) : null,
      'date' => date('d/m/Y', strtotime($this->created_at)),
      'send_admin' => $this->send_admin ?? null,
      'message' => $this->message ?? null,
      'data' => json_decode($this->data),
      'type' => $this->type ?? null,
      'code' => $this->code ?? null,
    ];
  }
}
