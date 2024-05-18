<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInput extends Model
{
  use HasFactory;

  protected $fillable = ['title_ar', 'title_en', 'title_fr', 'type', 'data', 'product_id'];
}
