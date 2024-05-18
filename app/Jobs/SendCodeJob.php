<?php

namespace App\Jobs;

use App\Mail\SendCodeMailable;
use App\Models\Product;
use App\Models\ProductCode;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCodeJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * Create a new job instance.
   */
  public function __construct(public User $user, public array $productDetails)
  {
    //
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    Mail::to($this->user->email)->send(new SendCodeMailable($this->user, $this->productDetails));
  }
}
