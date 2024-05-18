<?php

namespace App\Jobs;

use App\Mail\SendOrderMailable;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * Create a new job instance.
   */
  public function __construct(public Order $order, public string $message, public User $user)
  {
    //
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    Mail::to($this->user->email)->send(new SendOrderMailable($this->order, $this->message, $this->user));
  }
}
