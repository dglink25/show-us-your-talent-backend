<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoteRequest extends FormRequest{
    public function authorize(): bool{
        return true; // Géré dans le contrôleur
    }

    public function rules(): array
    {
        return [
            'candidature_id' => 'required|exists:candidatures,id',
            'email_votant' => 'required_if:votant_id,null|email|max:150',
            'methode_paiement' => 'required|in:mobile_money,carte_bancaire,wave,orange_money,mtn_money',
            'montant' => 'required|numeric|min:100|max:500000', // Min 100 FCFA, Max 50.000 FCFA
        ];
    }
}