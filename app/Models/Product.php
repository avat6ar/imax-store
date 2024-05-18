<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
  use HasFactory, HasSlug;

  protected $fillable = ['id', 'title_en', 'title_ar', 'title_fr', 'type_ar', 'type_fr', 'type_en', 'status', 'type', 'user_id', 'description_en', 'description_ar', 'description_fr', 'category_id', 'seen', 'sales_count'];

  public function getSlugOptions(): SlugOptions
  {
    return SlugOptions::create()
      ->generateSlugsFrom('title_en')
      ->saveSlugsTo('slug');
  }

  public function seo()
  {
    return $this->hasOne(ProductSeo::class);
  }

  public function images()
  {
    return $this->hasMany(ProductImage::class);
  }

  public function category()
  {
    return $this->belongsTo(ProductCategory::class, 'category_id');
  }

  public function questions()
  {
    return $this->hasMany(ProductQuestion::class);
  }

  public function reviews()
  {
    return $this->hasMany(ProductReview::class);
  }

  public function codes()
  {
    return $this->hasMany(ProductCode::class);
  }

  public function inputs()
  {
    return $this->hasMany(ProductInput::class);
  }

  public function prices()
  {
    return $this->hasOne(ProductPrice::class);
  }

  public function cart()
  {
    return $this->hasMany(Cart::class);
  }

  public function orders()
  {
    return $this->hasMany(Order::class);
  }

  public function getRateAttribute()
  {
    $totalRating = $this->reviews()->sum('rate');
    $numberOfReviews = $this->reviews()->count();

    if ($numberOfReviews > 0) {
      return round($totalRating / $numberOfReviews, 1);
    }

    return 0;
  }
}
