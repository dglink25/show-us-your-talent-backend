<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource{
    public function toArray(Request $request): array{
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenoms' => $this->prenoms,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'date_naissance' => $this->date_naissance,
            'age' => $this->date_naissance ? now()->diffInYears($this->date_naissance) : null,
            'sexe' => $this->sexe,
            'photo_url' => $this->photo_url,
            'origine' => $this->origine,
            'ethnie' => $this->ethnie,
            'universite' => $this->universite,
            'filiere' => $this->filiere,
            'annee_etude' => $this->annee_etude,
            'type_compte' => $this->type_compte,
            'compte_actif' => $this->compte_actif,
            'roles' => $this->getRoleNames(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}