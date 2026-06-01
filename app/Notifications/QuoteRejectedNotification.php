<?php

namespace App\Notifications;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuoteRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public Quote $quote) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_type' => 'quote_rejected',
            'title' => 'Quote Rejected',
            'message' => "{$this->quote->customer->name} rejected quote #{$this->quote->quote_number}.",
            'url' => route('owner.quotes.view', $this->quote),
        ];
    }
}
