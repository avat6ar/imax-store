<?php

namespace App\Http\Resources;

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
    $availability = $this->type == "automatic" ? ($this->codes()->where('status', 'available')->exists() ? 'available' : 'out of stock') : "available";


    return [
      'id' => $this->id,
      'slug' => $this->slug,
      'image' => asset($this->images[0]->image),
      'title_en' => $this->title_en,
      'title_ar' => $this->title_ar,
      'title_fr' => $this->title_fr,
      'description_en' => $this->description_en,
      'description_fr' => $this->description_fr,
      'description_ar' => $this->description_ar,
      'prices' => $this->prices,
      'availability' => $availability
    ];
  }
}
