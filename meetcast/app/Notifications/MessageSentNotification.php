<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Log;

class MessageSentNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        
        public User $user,
        public Message $message,
        public Conversation $conversation,

    )
    {
        //
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast'];
    }


    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'message_id' => $this->message->id,
            'conversation_id' => $this->conversation->id,
        ];
    }


}