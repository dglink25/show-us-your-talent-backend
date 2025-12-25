<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chat_room_id',
        'chat_message_id',
        'type',
        'message',
        'data',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function message()
    {
        return $this->belongsTo(ChatMessage::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    // Methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
        return $this;
    }

    public function getNotificationText()
    {
        $types = [
            'new_message' => 'Nouveau message dans',
            'user_joined' => 'a rejoint le chat',
            'user_left' => 'a quitté le chat',
            'promoteur_message' => 'Message du promoteur dans'
        ];

        return $types[$this->type] ?? 'Notification';
    }
}