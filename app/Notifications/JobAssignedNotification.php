<?php

namespace App\Notifications;

use App\Models\ServiceJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobAssignedNotification extends Notification
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
            'event_type' => 'job_assigned',
            'title' => 'New Job Assigned',
            'message' => "You have been assigned to \"{$this->job->name}\".",
            'url' => route('employee.jobs.show', $this->job),
        ];
    }
}
