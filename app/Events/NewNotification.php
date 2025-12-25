<?php

namespace App\Events;

use App\Models\ChatNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(ChatNotification $notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->notification->user_id);
    }

    public function broadcastAs()
    {
        return 'new.notification';
    }

    public function broadcastWith()
    {
        return [
            'notification' => $this->notification->load('room.category'),
            'unread_count' => ChatNotification::where('user_id', $this->notification->user_id)
                ->where('is_read', false)
                ->count()
        ];
    }
}