<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'message',
        'type',
        'metadata',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // Relations
    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function readers()
    {
        return $this->belongsToMany(User::class, 'chat_message_reads', 'chat_message_id', 'user_id')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    public function notifications()
    {
        return $this->hasMany(ChatNotification::class);
    }

    // Scopes
    public function scopeUnread($query, $userId)
    {
        return $query->where('user_id', '!=', $userId)
            ->whereDoesntHave('readers', function($q) use ($userId) {
                $q->where('user_id', $userId);
            });
    }

    public function scopeSince($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    // Methods
    public function markAsRead($userId)
    {
        if (!$this->readers()->where('user_id', $userId)->exists()) {
            $this->readers()->attach($userId, ['read_at' => now()]);
        }
        return $this;
    }

    public function isReadBy($userId)
    {
        return $this->readers()->where('user_id', $userId)->exists();
    }

    public function getFormattedTime()
    {
        return $this->created_at->format('H:i');
    }

    public function getFormattedDate()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

}