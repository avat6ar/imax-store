<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  use HasFactory;

  protected $fillable = [
    'currency',
    'total_amount',
    'payment_method',
    'payment_status',
    'data',
    'product_id',
    'user_id',
    'session_id',
    'order_id',
    'sending',
    'send_date',
    'send_admin',
    'message',
  ];

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function product()
  {
    return $this->belongsTo(Product::class, 'product_id');
  }
}
