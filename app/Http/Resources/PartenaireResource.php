<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartenaireResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'logo_url' => $this->logo_url,
            'website' => $this->website,
            'description' => $this->description,
            'type' => $this->type,
            'edition_id' => $this->edition_id,
            'ordre_affichage' => $this->ordre_affichage,
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}