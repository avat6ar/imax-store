<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCode extends Model
{
  use HasFactory;

  protected $fillable = ['code', 'status', 'product_id','user_id', 'expire_date'];
}
