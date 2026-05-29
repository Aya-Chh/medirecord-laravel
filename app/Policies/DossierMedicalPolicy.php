<?php

namespace App\Policies;

use App\Models\DossierMedical;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DossierMedicalPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // According to spec: GET/api/v1/dossiers -> admin, medecin (patients concernés), patient (les siens)
        // Admin can view all
        // Medecin can view dossiers of patients they have consultations with (i.e., patients they follow)
        // Patient can view their own dossiers
        return $user?->hasAnyRole(['admin', 'medecin', 'patient']) ?? false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DossierMedical $dossier): bool
    {
        // According to spec: GET/api/v1/dossiers/{id} -> admin, medecin concerné, patient concerné
        // Admin can view any
        if ($user->hasRole('admin')) {
            return true;
        }

        // Medecin can view if they are the medic of the consultation linked to the dossier, 
        // or if they have a consultation with the patient (to be safe, we check if they have any consultation with the patient)
        if ($user->hasRole('medecin')) {
            // Check if the medic has any consultation with the patient of this dossier
            return $user->medecin->consultations()->where('patient_id', $dossier->patient_id)->exists();
        }

        // Patient can view if they are the owner of the dossier
        if ($user->hasRole('patient')) {
            return $user->patient && $user->patient->id === $dossier->patient_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // According to spec: POST/api/v1/dossiers -> admin, medecin
        return $user->hasAnyRole(['admin', 'medecin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DossierMedical $dossier): bool
    {
        // According to spec: PUT/api/v1/dossiers/{id} -> admin, medecin concerné
        // We'll interpret "medecin concerné" as the medic of the consultation linked to the dossier (if any) 
        // or any medic that has a consultation with the patient (to align with the view policy).
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('medecin')) {
            // Check if the medic has any consultation with the patient of this dossier
            return $user->medecin->consultations()->where('patient_id', $dossier->patient_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DossierMedical $dossier): bool
    {
        // According to spec: DELETE/api/v1/dossiers/{id} -> admin
        return $user->hasRole('admin');
    }
}