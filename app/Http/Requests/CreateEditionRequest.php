<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEditionRequest extends FormRequest{
    public function authorize(): bool{
        return $this->user()->hasRole('promoteur') || $this->user()->hasRole('admin');
    }

    public function rules(): array{
        return [
            'nom' => 'required|string|max:200',
            'annee' => 'required|integer|min:2024|max:2030',
            'numero_edition' => 'required|integer|min:1',
            'description' => 'nullable|string|max:2000',
            'date_debut_inscriptions' => 'nullable|date|after:today',
            'date_fin_inscriptions' => 'nullable|date|after:date_debut_inscriptions',
        ];
    }
}