<?php

namespace App\Notifications;

use App\Models\ServiceJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ServiceOrderCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public ServiceJob $job) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $customer = $this->job->customer;
        $customerName = $customer ? trim(($customer->first_name ?? '').' '.($customer->last_name ?? '')) : 'A customer';
        $deadline = $this->job->deadline?->format('M d, Y');

        return [
            'event_type' => 'service_order_created',
            'title' => 'New Service Order',
            'message' => "{$customerName} requested \"{$this->job->name}\"".($deadline ? " with deadline {$deadline}." : '.'),
            'url' => route('owner.jobs.show', $this->job),
        ];
    }
}
