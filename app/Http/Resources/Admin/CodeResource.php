<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CodeResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'code' => $this->code,
      'email' => $this->email,
      'amount' => $this->amount,
      'is_used' => $this->is_used,
      'created_at' => date('Y-m-d H:i:s', strtotime($this->created_at)),
      'expired_at' => $this->expired_at ? date('Y-m-d H:i:s', strtotime($this->expired_at)) : 'N/A',
    ];
  }
}
