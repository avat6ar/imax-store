<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductViewResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $orders = $this->orders()->get();
    $customers = [];
    foreach ($orders as $order) {
      if ($order->payment_status == "complete") {
        $user = $order->user()->first();
        $customers[] = [
          'id' => $user->id,
          'name' => $user->name,
          'email' => $user->email,
          'date' => date('d/m/Y', strtotime($order->created_at))
        ];
      }
    }

    return [
      'id' => $this->id,
      'title_en' => $this->title_en,
      'title_fr' => $this->title_fr,
      'title_ar' => $this->title_ar,
      'description_ar' => $this->description_ar,
      'description_en' => $this->description_en,
      'description_fr' => $this->description_fr,
      'category' => $this->category,
      'category_id' => $this->category_id,
      'prices' => $this->prices,
      'type' => $this->type_en,
      'questions' => $this->questions,
      'codes' => $this->codes,
      'inputs' => $this->inputs,
      'seo' => $this->seo,
      'images' => $this->images->map(function ($image) {
        return [
          'id' => $image->id,
          'image' => asset($image->image)
        ];
      }),
      'reviews' => $this->reviews->map(function ($review) {
        return [
          'id' => $review->id,
          'rate' => $review->rate,
          'review' => $review->review,
          'publish' => $review->publish,
          'username' => $review->user->name,
          'date' => $review->created_at->format('F j, Y')
        ];
      }),
      'sales_count' => $this->sales_count,
      'seen' => $this->seen,
      'comments' => count($this->reviews),
      'rate' => $this->getRateAttribute(),
      'customers' => $customers
    ];
  }
}
