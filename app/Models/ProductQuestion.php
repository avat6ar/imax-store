<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductQuestion extends Model
{
  use HasFactory;

  protected $fillable = ['question_ar', 'question_en', 'question_fr', 'answer_ar', 'answer_en', 'answer_fr', 'product_id'];
}
