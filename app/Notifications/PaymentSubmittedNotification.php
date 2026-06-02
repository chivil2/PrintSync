<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $customer = $this->payment->customer;
        $customerName = trim(($customer->first_name ?? '').' '.($customer->last_name ?? ''));

        $jobId = $this->payment->service_job_id;

        return [
            'event_type' => 'payment_submitted',
            'title' => 'Payment Received',
            'message' => "{$customerName} paid ₱".number_format((float) $this->payment->amount, 2)
                ." via GCash for quote #{$this->payment->quote->quote_number}.",
            'url' => $jobId ? route('owner.jobs.show', $jobId) : route('owner.payments'),
            'service_job_id' => $jobId,
        ];
    }
}
