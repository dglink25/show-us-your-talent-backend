<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'user_id',
        'email_payeur',
        'montant',
        'devise',
        'methode',
        'statut',
        'metadata',
        'transaction_id',
        'paye_le'
    ];

    protected $casts = [
        'metadata' => 'array',
        'paye_le' => 'datetime',
        'montant' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}