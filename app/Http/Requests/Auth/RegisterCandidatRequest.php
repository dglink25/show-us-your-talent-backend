<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCandidatRequest extends FormRequest
{
    public function authorize(): bool{
        return true; // Tout le monde peut s'inscrire
    }

    public function rules(): array{
        return [
            'nom' => 'required|string|max:100',
            'prenoms' => 'required|string|max:200',
            'email' => 'required|email|unique:users,email',
            'date_naissance' => 'required|date|before:today',
            'sexe' => 'required|in:M,F,Autre',
            'telephone' => 'required|string|max:20',
            'photo_url' => 'nullable|url|max:500',
            'origine' => 'required|string|max:100',
            'ethnie' => 'nullable|string|max:100',
            'universite' => 'required|string|max:200',
            'filiere' => 'required|string|max:150',
            'annee_etude' => 'required|string|max:50',
            
            // Données de la candidature
            'edition_id' => 'required|exists:editions,id',
            'category_id' => 'required|exists:categories,id',
            'video_url' => 'required|url|max:500',
            'description_talent' => 'required|string|min:50|max:1000',
        ];
    }

    public function messages(): array{
        return [
            'email.unique' => 'Cet email est déjà utilisé.',
            'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'video_url.required' => 'Une vidéo de présentation est obligatoire.',
        ];
    }
}