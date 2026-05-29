<?php

namespace App\Policies;

use App\Models\Medecin;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedecinPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user can view the list of medecins (according to spec: GET/api/v1/medecins -> Tous authentifiés)
        return $user->isNotNull();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Medecin $medecin): bool
    {
        // Any authenticated user can view a specific medecin (according to spec: GET/api/v1/medecins/{id} -> Tous authentifiés)
        return $user->isNotNull();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin can create medecin records (according to spec: POST/api/v1/medecins -> admin)
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Medecin $medecin): bool
    {
        // Admin can update any medecin, or the medecin himself can update his own record (according to spec: PUT/api/v1/medecins/{id} -> admin, ou medecin lui-même)
        return $user->hasRole('admin') || ($user->isNotNull() && $user->medecin && $user->medecin->id === $medecin->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Medecin $medecin): bool
    {
        // Only admin can delete medecin records (according to spec: DELETE/api/v1/medecins/{id} -> admin)
        return $user->hasRole('admin');
    }
}