<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $title_product = [
      'title_en' => $this->product->title_en,
      'title_ar' => $this->product->title_ar,
      'title_fr' => $this->product->title_fr,
    ];

    return [
      'id' => $this->id,
      'review' => $this->review,
      'username' => $this->user->name,
      'email' => $this->user->email,
      'date' => date('d/m/Y', strtotime($this->created_at)),
      'rate' => $this->rate,
      'title_product' => $title_product,
      'publish' => $this->publish,
    ];
  }
}
