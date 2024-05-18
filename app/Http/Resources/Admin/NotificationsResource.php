<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationsResource extends JsonResource
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
      'title' => $this->data['title'],
      'date' => date('Y-m-d H:i:s', strtotime($this->created_at)),
      'message' => $this->data['message'],
      'user_name' => $this->data['user_name'] ?? null,
      'reed' => $this->reed_at ? "true" : "false",
    ];
  }
}
