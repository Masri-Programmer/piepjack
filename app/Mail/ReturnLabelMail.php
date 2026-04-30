<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReturnLabelMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $order,
        public $returning,
        public string $labelUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Your Return Label for Order #') . $this->order->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.returnLabel',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
