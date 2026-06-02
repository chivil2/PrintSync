<?php

namespace App\Notifications;

use App\Models\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewChatMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public Conversation $conversation, public string $preview) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $sender = $this->conversation->customer_id === $notifiable->id
            ? $this->conversation->owner
            : $this->conversation->customer;

        $url = $notifiable->hasRole('owner')
            ? route('owner.chat.show', $this->conversation)
            : route('customer.chat.show', $this->conversation);

        return [
            'event_type' => 'chat_message',
            'title' => 'New message from '.$sender->name,
            'message' => $this->preview,
            'url' => $url,
        ];
    }
}
