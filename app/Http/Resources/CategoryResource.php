<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
      'category_en' => $this->category_en,
      'category_ar' => $this->category_ar,
      'category_fr' => $this->category_fr,
      'product_length' => count($this->products),
      'image' => asset($this->image),
    ];
  }
}
