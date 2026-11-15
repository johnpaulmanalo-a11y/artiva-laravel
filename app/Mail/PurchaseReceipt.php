<?php

namespace App\Mail;

use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Receipt $receipt)
    {
        $this->receipt->loadMissing('order.user', 'order.items', 'payment');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Artiva purchase receipt '.$this->receipt->receipt_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.receipts.purchase',
        );
    }
}
