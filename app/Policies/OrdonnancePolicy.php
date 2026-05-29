<?php

namespace App\Policies;

use App\Models\Ordonnance;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrdonnancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    // According to spec: GET/api/v1/ordonnances -> admin, medecin (les siennes), patient (les siennes)
    {
        // Admin can view all
        // Medecin can view their own ordonnances
        // Patient can view their own ordonnances
        return $user?->hasAnyRole(['admin', 'medecin', 'patient']) ?? false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ordonnance $ordonnance): bool
    {
        // According to spec: GET/api/v1/ordonnances/{id} -> admin, medecin concerné, patient concerné
        // Admin can view any
        if ($user->hasRole('admin')) {
            return true;
        }

        // Medecin can view if they are the medic of the ordonnance
        if ($user->hasRole('medecin')) {
            return $user->medecin && $user->medecin->id === $ordonnance->medecin_id;
        }

        // Patient can view if they are the patient of the ordonnance
        if ($user->hasRole('patient')) {
            return $user->patient && $user->patient->id === $ordonnance->patient_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // According to spec: POST/api/v1/ordonnances -> admin, medecin
        return $user->hasAnyRole(['admin', 'medecin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ordonnance $ordonnance): bool
    {
        // According to spec: PUT/api/v1/ordonnances/{id} -> admin, medecin concerné
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('medecin')) {
            return $user->medecin && $user->medecin->id === $ordonnance->medecin_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ordonnance $ordonnance): bool
    {
        // According to spec: DELETE/api/v1/ordonnances/{id} -> admin
        return $user->hasRole('admin');
    }
}