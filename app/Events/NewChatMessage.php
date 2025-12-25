<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $room;

    public function __construct(ChatMessage $message, ChatRoom $room)
    {
        $this->message = $message;
        $this->room = $room;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('chat.room.' . $this->room->id);
    }

    public function broadcastAs()
    {
        return 'new.message';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message->load('user'),
            'room_id' => $this->room->id,
            'timestamp' => now()->toISOString()
        ];
    }
}