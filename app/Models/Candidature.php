<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidat_id',
        'edition_id',
        'category_id',
        'video_url',
        'description_talent',
        'statut',
        'phase_actuelle',
        'note_jury',
        'nombre_votes',
        'motif_refus',
        'valide_par',
        'valide_le'
    ];

    protected $casts = [
        'valide_le' => 'datetime',
        'nombre_votes' => 'integer'
    ];

    // Relations
    public function candidat()
    {
        return $this->belongsTo(User::class, 'candidat_id');
    }

    public function edition()
    {
        return $this->belongsTo(Edition::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}