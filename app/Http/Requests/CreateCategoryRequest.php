<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest{
    public function authorize(): bool
    {
        $edition = $this->route('edition');
        $user = $this->user();
        
        // Seul le promoteur de l'édition ou admin peut créer des catégories
        return $user->hasRole('admin') || 
               ($user->hasRole('promoteur') && $edition->promoteur_id == $user->id);
    }

    public function rules(): array{
        return [
            'nom' => 'required|string|max:150|unique:categories,nom,NULL,id,edition_id,' . $this->route('edition')->id,
            'description' => 'nullable|string|max:1000',
            'ordre_affichage' => 'integer|min:0',
            'active' => 'boolean',
        ];
    }
}