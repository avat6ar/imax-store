<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $product = $this->product;

    return [
      'id' => $product->id,
      'slug' => $product->slug,
      'title_en' => $product->title_en,
      'title_fr' => $product->title_fr,
      'title_ar' => $product->title_ar,
      'category' => $product->category,
      'prices' => $product->prices,
      'image' => asset($product->images[0]->image),
    ];
  }
}
