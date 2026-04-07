<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public array $orderData;

    public function __construct(array $orderData)
    {
        $this->orderData = $orderData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Auftragsbestätigung',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orderConfirmation',
            with: [
                'order' => $this->orderData['order'],
                'user' => $this->orderData['user'],
                'address' => $this->orderData['address'],
                'products' => $this->orderData['products'],
                'subtotal' => $this->orderData['subtotal'] ?? 0,
                'discount' => $this->orderData['discount'] ?? 0,
                'shipping' => $this->orderData['shipping'] ?? 0,
                'total' => $this->orderData['total'] ?? 0,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
