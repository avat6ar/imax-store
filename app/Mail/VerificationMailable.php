<?php

namespace App\Mail;

use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationMailable extends Mailable
{
  use Queueable, SerializesModels;

  public function __construct(
    public User $user,
    public VerificationCode $code,
  )
  {
  }

  public function envelope(): Envelope
  {
    return new Envelope(
      subject: __('emailVerification.subject'),
    );
  }

  public function content(): Content
  {
    return new Content(
      view: 'mail.email_verification'
    );
  }

  public function attachments(): array
  {
    return [];
  }
}
