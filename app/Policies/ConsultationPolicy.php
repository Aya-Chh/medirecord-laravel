<?php

namespace App\Policies;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsultationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Admin can view all consultations
        // Medecin can view their own consultations
        // Patient can view their own consultations
        // According to spec: GET/api/v1/consultations -> admin (toutes), medecin (les siennes), patient (les siennes)
        return $user?->hasAnyRole(['admin', 'medecin', 'patient']) ?? false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Consultation $consultation): bool
    {
        // Admin can view any consultation
        // Medecin can view if they are the medic of the consultation
        // Patient can view if they are the patient of the consultation
        // According to spec: GET/api/v1/consultations/{id} -> admin, medecin concerné, patient concerné
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('medecin')) {
            return $user->medecin && $user->medecin->id === $consultation->medecin_id;
        }

        if ($user->hasRole('patient')) {
            return $user->patient && $user->patient->id === $consultation->patient_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // According to spec: POST/api/v1/consultations -> admin, medecin
        return $user->hasAnyRole(['admin', 'medecin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Consultation $consultation): bool
    {
        // According to spec: PUT/api/v1/consultations/{id} -> admin, medecin concerné
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('medecin')) {
            return $user->medecin && $user->medecin->id === $consultation->medecin_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Consultation $consultation): bool
    {
        // According to spec: DELETE/api/v1/consultations/{id} -> admin
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the statut of the model.
     * We are adding a specific method for the statut update because the spec has a separate route for it.
     */
    public function updateStatut(User $user, Consultation $consultation): bool
    {
        // According to spec: PATCH/api/v1/consultations/{id}/statut -> admin, medecin concerné
        // We can reuse the same logic as update for this specific action.
        return $this->update($user, $consultation);
    }
}