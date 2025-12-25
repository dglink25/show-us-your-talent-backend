<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditionPhase extends Model
{
    use HasFactory;

    protected $fillable = [
        'edition_id',
        'nom',
        'numero_phase',
        'description',
        'date_debut',
        'date_fin',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime'
    ];

    public function edition()
    {
        return $this->belongsTo(Edition::class);
    }
}