<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CandidatVoteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nom_complet' => $this->nom_complet,
            'sexe' => $this->sexe,
            'photo_url' => $this->photo_url,
            'ethnie' => $this->ethnie,
            'universite' => $this->universite,
            'entite' => $this->entite,
            'filiere' => $this->filiere,
            'nombre_votes' => $this->votes_count ?? 0,
            'candidature_id' => $this->candidature->id ?? null,
            'categorie_id' => $this->candidature->categorie_id ?? null,
            'created_at' => $this->created_at,
        ];
    }
}