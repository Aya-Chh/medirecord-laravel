<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatientPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Admin and medecin can view the list of patients (according to spec: GET/api/v1/patients -> admin, medecin)
        return $user?->hasAnyRole(['admin', 'medecin']) ?? false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Patient $patient): bool
    {
        // Admin can view any patient
        // Medecin can view patients that they have consultations with (via the spec: medecin qui le suit)
        // Patient can view their own record
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('medecin')) {
            // Check if the medic has any consultations with this patient
            return $user->medecin->consultations()->where('patient_id', $patient->id)->exists();
        }

        if ($user->hasRole('patient')) {
            // Patient can view their own record
            return $user->patient && $user->patient->id === $patient->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin can create patient records (according to spec: POST/api/v1/patients -> admin)
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Patient $patient): bool
    {
        // Admin can update any patient
        // Patient can update their own record
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('patient')) {
            return $user->patient && $user->patient->id === $patient->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Patient $patient): bool
    {
        // Only admin can delete patient records (according to spec: DELETE/api/v1/patients/{id} -> admin)
        return $user->hasRole('admin');
    }
}