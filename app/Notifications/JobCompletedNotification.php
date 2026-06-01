<?php

namespace App\Notifications;

use App\Models\ServiceJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(public ServiceJob $job, public string $recipientRole) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $url = $this->recipientRole === 'owner'
            ? route('owner.jobs.show', $this->job)
            : route('customer.orders.show', $this->job);

        return [
            'event_type' => 'job_completed',
            'title' => 'Job Completed',
            'message' => "\"{$this->job->name}\" has been marked as completed.",
            'url' => $url,
        ];
    }
}
