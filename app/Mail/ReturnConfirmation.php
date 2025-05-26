<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReturnConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $returnData;

    public function __construct($returnData)
    {
        $this->returnData = $returnData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Return Request Confirmation'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.returnConfirmation',
            with: [
                'returnData' => $this->returnData,
                'return' => $this->returnData['return'],
                'customer' => $this->returnData['customer'],
                'items' => $this->returnData['items'],
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
