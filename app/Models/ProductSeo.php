<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSeo extends Model
{
  use HasFactory;

  protected $fillable = ['title_en', 'title_ar', 'title_fr', 'description_en', 'description_fr', 'description_ar', 'keywords_en', 'keywords_fr', 'keywords_ar', 'product_id'];
}
