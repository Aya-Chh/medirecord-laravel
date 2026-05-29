<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Controller pour la gestion des utilisateurs et profils.
 */
class UserController extends Controller
{
    /**
     * Afficher une liste paginée des utilisateurs.
     * Seulement accessible par les admins.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $users = User::with(['profile', 'medecin', 'patient'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->input('role'), function ($query, $role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->orderBy('id')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'data' => UserResource::collection($users),
            'meta' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
            ],
        ]);
    }

    /**
     * Afficher les détails d'un utilisateur.
     * Accessible par l'admin ou l'utilisateur lui-même.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        return response()->json([
            'data' => new UserResource($user->load(['profile', 'medecin', 'patient'])),
        ]);
    }

    /**
     * Mettre à jour un utilisateur.
     * Accessible par l'admin ou l'utilisateur lui-même.
     * Note: La mise à jour du profil est séparée via le ProfileController.
     * Ici, nous ne mettons à jour que les champs de l'utilisateur (name, email, phone, etc.).
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        // Validation des champs de l'utilisateur
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:20',
            // Note: Le mot de passe devrait être mis à jour via une route spécifique (ex: /api/auth/reset-password)
            // donc nous ne le gérons pas ici.
        ]);

        $user->update($request->only(['name', 'email', 'phone']));

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'data' => new UserResource($user->load(['profile', 'medecin', 'patient'])),
        ]);
    }

    /**
     * Supprimer un utilisateur (soft delete).
     * Seulement accessible par les admins.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès',
        ]);
    }
}