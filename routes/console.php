<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\Broadcast;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Broadcast::channel('chat.room.{roomId}', function ($user, $roomId) {
    $room = ChatRoom::find($roomId);
    return $room && $room->hasParticipant($user->id);
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});