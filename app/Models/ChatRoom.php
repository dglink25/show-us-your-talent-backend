<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'edition_id',
        'status'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function edition()
    {
        return $this->belongsTo(Edition::class);
    }

    public function participants()
    {
        return $this->hasMany(ChatParticipant::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class)->latest();
    }

    public function unreadMessages()
    {
        return $this->hasMany(ChatMessage::class)->where('is_read', false);
    }

    public function promoteurs()
    {
        return $this->participants()->where('role', 'promoteur');
    }

    public function candidats()
    {
        return $this->participants()->where('role', 'candidat');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForEdition($query, $editionId)
    {
        return $query->where('edition_id', $editionId);
    }

    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Methods
    public function addParticipant($userId, $role = 'candidat')
    {
        return $this->participants()->create([
            'user_id' => $userId,
            'role' => $role
        ]);
    }

    public function hasParticipant($userId)
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    public function notifications()
    {
        return $this->hasMany(ChatNotification::class);
    }

    public function getUnreadCountForUser($userId)
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->whereDoesntHave('readers', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();
    }

}