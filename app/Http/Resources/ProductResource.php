<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
      'title_en' => $this->title_en,
      'title_fr' => $this->title_fr,
      'title_ar' => $this->title_ar,
      'type_en' => $this->type_en,
      'type_fr' => $this->type_fr,
      'type_ar' => $this->type_ar,
      'description_ar' => $this->description_ar,
      'description_en' => $this->description_en,
      'description_fr' => $this->description_fr,
      'category' => $this->category,
      'prices' => $this->prices,
      'type' => $this->type_en,
      'questions' => $this->questions,
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
          'message' => $review->message,
          'username' => $review->user->name,
          'date' => $review->created_at->format('F j, Y')
        ];
      }),
      'comments' => count($this->reviews),
      'rate' => $this->getRateAttribute(),
    ];
  }
}
