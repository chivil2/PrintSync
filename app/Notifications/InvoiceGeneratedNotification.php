<?php

namespace App\Notifications;

use App\Models\ServiceJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvoiceGeneratedNotification extends Notification
{
    use Queueable;

    public function __construct(public ServiceJob $job) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_type' => 'invoice_generated',
            'title' => 'Invoice Ready',
            'message' => "Your invoice for \"{$this->job->name}\" is available for download.",
            'url' => route('customer.orders.show', $this->job),
        ];
    }
}
