<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification
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
            'event_type' => 'payment_rejected',
            'title' => 'Payment Rejected',
            'message' => "Your GCash payment for quote #{$this->payment->quote->quote_number} was rejected. Reason: {$this->payment->rejection_reason}. Please submit a new payment.",
            'url' => route('customer.quotes.show', $this->payment->quote),
        ];
    }
}
