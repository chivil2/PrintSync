<?php

namespace App\Notifications;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuoteSentNotification extends Notification
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
            'event_type' => 'quote_sent',
            'title' => 'New Quote Ready for Review',
            'message' => "Your quote #{$this->quote->quote_number} is ready. Please review and approve.",
            'url' => route('customer.quotes.show', $this->quote),
        ];
    }
}
