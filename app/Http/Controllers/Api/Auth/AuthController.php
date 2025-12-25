<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Login pour admin et promoteur (les candidats n'ont pas de mot de passe initial)
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects......'],
            ]);
        }

        // Vérifier que le compte est actif
        if (!$user->compte_actif) {
            return response()->json(['message' => 'Votre compte a été désactivé.'], 403);
        }

        // Vérifier que ce n'est pas un candidat sans mot de passe
        if ($user->type_compte === 'candidat' && !$user->password) {
            return response()->json(['message' => 'Veuillez d\'abord définir un mot de passe.'], 403);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'user' => $user->load('roles'),
            'token' => $token,
        ]);
    }

    // Définir le mot de passe pour un candidat (première connexion)
    public function setPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'device_name' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->where('type_compte', 'candidat')->first();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        if ($user->password) {
            return response()->json(['message' => 'Mot de passe déjà défini.'], 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'message' => 'Mot de passe défini avec succès.',
            'user' => $user->load('roles'),
            'token' => $token,
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    // Récupérer l'utilisateur connecté
    public function me(Request $request){
        return response()->json($request->user()->load('roles'));
    }
}