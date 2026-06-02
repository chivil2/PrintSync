<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentVerifiedNotification extends Notification
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_type' => 'payment_verified',
            'title' => 'Payment Verified',
            'message' => 'Your GCash payment of ₱'.number_format((float) $this->payment->amount, 2)
                ." for quote #{$this->payment->quote->quote_number} has been verified. Work will begin shortly.",
            'url' => route('customer.quotes.show', $this->payment->quote),
        ];
    }
}
