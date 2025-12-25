<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidatureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'statut' => $this->statut,
            'phase_actuelle' => $this->phase_actuelle,
            'video_url' => $this->video_url,
            'description_talent' => $this->description_talent,
            'note_jury' => $this->when($request->user() && ($request->user()->hasRole('admin') || $request->user()->hasRole('promoteur')), 
                $this->note_jury
            ),
            'nombre_votes' => $this->nombre_votes,
            'motif_refus' => $this->when($request->user() && ($request->user()->id == $this->candidat_id || $request->user()->hasRole('admin') || $request->user()->hasRole('promoteur')), 
                $this->motif_refus
            ),
            'valide_le' => $this->valide_le,
            'candidat' => new UserResource($this->whenLoaded('candidat')),
            'edition' => new EditionResource($this->whenLoaded('edition')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'validateur' => new UserResource($this->whenLoaded('validateur')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}