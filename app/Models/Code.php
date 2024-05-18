<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
  use HasFactory;

  protected $fillable = ['code', 'email', 'amount', 'is_used', 'expired_at'];

  public static function createCodes($count, $amount)
  {
    for ($i = 0; $i < $count; $i++)
    {
      $code = new self();
      $code->code = \Illuminate\Support\Str::random(10);
      $code->amount = $amount;
      $code->save();
    }
  }

  public static function checkCode($code)
  {
    return self::where('code', $code)->where('is_used', false)->first();
  }

  public function markAsUsed($email)
  {
    $this->is_used = true;
    $this->email = $email;
    $this->expired_at = now();
    $this->save();
  }
}
