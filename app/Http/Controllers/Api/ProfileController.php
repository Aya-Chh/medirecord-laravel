<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use Illuminate\Http\Request;

/**
 * Controller pour la gestion des profils.
 */
class ProfileController extends Controller
{
    /**
     * Afficher le profil d'un utilisateur.
     * Accessible par l'admin ou l'utilisateur lui-même.
     */
    public function show(Request $request, $userId)
    {
        // Récupérer l'utilisateur
        $user = \App\Models\User::findOrFail($userId);

        // Vérifier l'autorisation via la policy (UserPolicy pour le user, mais on veut le profil)
        // On peut réutiliser la policy User pour le user, car le profil appartient à l'utilisateur.
        $this->authorize('view', $user);

        // Charger le profil de l'utilisateur
        $profile = $user->profile;

        return response()->json([
            'data' => new ProfileResource($profile),
        ]);
    }

    /**
     * Mettre à jour le profil d'un utilisateur.
     * Accessible par l'admin ou l'utilisateur lui-même.
     */
    public function update(UpdateProfileRequest $request, $userId)
    {
        // Récupérer l'utilisateur
        $user = \App\Models\User::findOrFail($userId);

        // Vérifier l'autorisation
        $this->authorize('update', $user);

        // Mettre à jour le profil
        $profile = $user->profile;
        $profile->update($request->validated());

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'data' => new ProfileResource($profile->fresh()),
        ]);
    }
}