<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'social_id',
    'social_type',
    'email_verified_at',
    'social_status',
    'status',
    'balance',
    'role'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'social_id',
    'social_type',
    'email_verified_at',
    'social_status',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  public function verificationCode()
  {
    return $this->hasOne(VerificationCode::class);
  }

  public function isAdmin()
  {
    return $this->role === 'admin' || $this->role === 'super_admin';
  }

  public function cart()
  {
    return $this->hasMany(Cart::class);
  }

  public function orders()
  {
    return $this->hasMany(Order::class);
  }

  public function wishlists()
  {
    return $this->hasMany(Wishlist::class);
  }
}
