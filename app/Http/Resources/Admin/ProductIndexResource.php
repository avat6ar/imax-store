<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductIndexResource extends JsonResource
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
      'slug' => $this->slug,
      'image' => asset($this->images[0]->image),
      'title' => $this->title_en,
      'category' => $this->category->category_en,
      'price' => $this->prices->usd,
      'rate' => $this->getRateAttribute(),
    ];
  }
}
