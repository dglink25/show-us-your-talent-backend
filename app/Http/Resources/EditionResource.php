<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'annee' => $this->annee,
            'numero_edition' => $this->numero_edition,
            'description' => $this->description,
            'statut' => $this->statut,
            'inscriptions_ouvertes' => $this->inscriptions_ouvertes,
            'date_debut_inscriptions' => $this->date_debut_inscriptions,
            'date_fin_inscriptions' => $this->date_fin_inscriptions,
            'votes_ouverts' => $this->votes_ouverts,
            'date_debut_votes' => $this->date_debut_votes,
            'date_fin_votes' => $this->date_fin_votes,
            'promoteur' => new UserResource($this->whenLoaded('promoteur')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'phases' => EditionPhaseResource::collection($this->whenLoaded('phases')),
            'partenaires' => PartenaireResource::collection($this->whenLoaded('partenaires')),
            'nombre_candidatures' => $this->when($request->user() && ($request->user()->hasRole('admin') || $request->user()->hasRole('promoteur')), 
                function() {
                    return $this->candidatures()->count();
                }
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}