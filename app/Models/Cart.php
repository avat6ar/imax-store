<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
  use HasFactory;

  protected $fillable = ['product_id', 'user_id', 'data'];

  public function user()
  {
    return $this->belongsTo(User::class,'user_id');
  }

  public function product()
  {
    return $this->belongsTo(Product::class, 'product_id');
  }

  public function scopeCompleted($query)
  {
    return $query->where('payment_status', 'complete');
  }

  public function scopePending($query)
  {
    return $query->where('payment_status', 'pending');
  }

  public function scopeCancelled($query)
  {
    return $query->where('payment_status', 'cancelled');
  }
}
