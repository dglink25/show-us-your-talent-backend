<?php
// app/Http/Requests/RegisterCandidatRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterCandidatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autoriser tout le monde à s'inscrire
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|min:2|max:50',
            'prenoms' => 'required|string|min:2|max:100',
            'email' => 'required|email|unique:candidats,email',
            'date_naissance' => 'required|date|before:-16 years',
            'sexe' => 'required|in:M,F,Autre',
            'telephone' => 'required|string|min:8|max:20|regex:/^[0-9+\s]+$/',
            'origine' => 'required|string|min:2|max:100',
            'ethnie' => 'nullable|string|max:100',
            'universite' => 'required|string|min:2|max:200',
            'filiere' => 'required|string|min:2|max:200',
            'annee_etude' => 'required|in:Licence 1,Licence 2,Licence 3,Master 1,Master 2,Doctorat,Autre',
            'edition_id' => 'required|integer|exists:editions,id',
            'category_id' => 'required|integer|exists:categories,id',
            'video_url' => 'nullable|url|max:500',
            'description_talent' => 'required|string|min:100|max:2000',
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB
            'video' => 'required|file|mimes:mp4,mov,avi,webm|max:102400', // 100MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est requis',
            'nom.min' => 'Le nom doit contenir au moins 2 caractères',
            'prenoms.required' => 'Les prénoms sont requis',
            'email.required' => 'L\'email est requis',
            'email.unique' => 'Cet email est déjà utilisé',
            'date_naissance.required' => 'La date de naissance est requise',
            'date_naissance.before' => 'Vous devez avoir au moins 16 ans',
            'telephone.required' => 'Le téléphone est requis',
            'telephone.regex' => 'Le numéro de téléphone est invalide',
            'photo.required' => 'La photo de profil est requise',
            'photo.max' => 'La photo ne doit pas dépasser 5MB',
            'video.required' => 'La vidéo de présentation est requise',
            'video.max' => 'La vidéo ne doit pas dépasser 100MB',
            'description_talent.min' => 'La description doit contenir au moins 100 caractères',
            'edition_id.exists' => 'L\'édition sélectionnée n\'existe pas',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom',
            'prenoms' => 'prénoms',
            'email' => 'email',
            'date_naissance' => 'date de naissance',
            'sexe' => 'sexe',
            'telephone' => 'téléphone',
            'origine' => 'origine',
            'ethnie' => 'ethnie',
            'universite' => 'université',
            'filiere' => 'filière',
            'annee_etude' => 'année d\'étude',
            'edition_id' => 'édition',
            'category_id' => 'catégorie',
            'video_url' => 'URL de la vidéo',
            'description_talent' => 'description du talent',
            'photo' => 'photo',
            'video' => 'vidéo',
        ];
    }
}