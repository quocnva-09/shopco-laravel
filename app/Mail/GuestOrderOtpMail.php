<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuestOrderOtpMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly Order $order,
        public readonly string $otp
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Checkout Verification Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.guest_order_otp',
            with: [
                'order' => $this->order,
                'otp' => $this->otp,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
