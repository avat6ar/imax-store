<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
  use HasFactory;

  protected $fillable = [
    'usd',
    'sar',
    'aed',
    'kwd',
    'eur',
    'dzd',
    'egp',
    'imx',
    'product_id',
  ];
}
