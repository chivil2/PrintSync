<?php

namespace App\Mail;

use App\Models\ServiceJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class InvoiceGenerated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ServiceJob $serviceJob,
    ) {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice from PrintSync',
        );
    }

    public function content(): Content
    {
        $quote = $this->serviceJob->quote;

        return new Content(
            markdown: 'emails.invoices.generated',
            with: [
                'serviceJob' => $this->serviceJob,
                'quote' => $quote,
                'customer' => $this->serviceJob->customer,
            ],
        );
    }

    public function attachments(): array
    {
        if (! $this->serviceJob->invoice_path) {
            return [];
        }

        return [
            Attachment::fromPath(
                Storage::path($this->serviceJob->invoice_path),
            ),
        ];
    }
}
