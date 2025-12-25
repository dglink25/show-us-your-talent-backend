<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partenaire extends Model{
    use HasFactory;

    protected $fillable = [
        'nom',
        'logo_url',
        'website',
        'description',
        'type',
        'edition_id',
        'ordre_affichage',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function edition()
    {
        return $this->belongsTo(Edition::class);
    }
}