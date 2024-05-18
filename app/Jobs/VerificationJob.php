<?php

namespace App\Jobs;

use App\Mail\VerificationMailable;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class VerificationJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * Create a new job instance.
   */
  public function __construct(
    public User $user,
    public VerificationCode $code,
  )
  {
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    Mail::to($this->user->email)->send(new VerificationMailable($this->user, $this->code));
  }
}
