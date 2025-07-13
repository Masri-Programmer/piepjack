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

    /**
     * The data for the return.
     *
     * @var array
     */
    public $returnData;

    public function __construct(array $returnData)
    {
        $this->returnData = $returnData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Return Request Confirmation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.returnConfirmation',
            with: [
                'return'  => $this->returnData['return'],
                'user'    => $this->returnData['user'],
                'address' => $this->returnData['address'],
                'items'   => $this->returnData['items'],
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
