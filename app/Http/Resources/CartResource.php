<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $product =$this->product;

    return [
      'id' => $this->id,
      'slug' => $product->slug,
      'prices' => $product->prices,
      'image' => asset($product->images[0]->image),
      'title_en' => $product->title_en,
      'title_ar' => $product->title_ar,
      'title_fr' => $product->title_fr,
    ];
  }
}
