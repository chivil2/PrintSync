<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteSent extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quote $quote,
    ) {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Quote from PrintSync - '.$this->quote->quote_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quotes.sent',
            with: [
                'quote' => $this->quote,
                'customer' => $this->quote->customer,
                'lineItems' => $this->quote->lineItems,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
