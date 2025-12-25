<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditionPhaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'numero_phase' => $this->numero_phase,
            'description' => $this->description,
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin,
            'active' => $this->active,
            'edition_id' => $this->edition_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}