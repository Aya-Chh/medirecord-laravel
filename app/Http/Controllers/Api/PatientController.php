<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StorePatientRequest;
use App\Http\Requests\Patient\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Http\Resources\ConsultationResource;
use App\Http\Resources\DossierMedicalResource;
use App\Http\Resources\OrdonnanceResource;
use App\Models\Patient;
use Illuminate\Http\Request;

/**
 * Controller pour la gestion des patients.
 */
class PatientController extends Controller
{
    /**
     * Afficher une liste paginée des patients.
     * Accessible par les admins et les médecins (qui voient leurs patients).
     * Selon la spécification: GET/api/v1/patients -> admin, medecin
     * Cependant, note que la spécification dit aussi: GET/api/v1/patients/{id} -> admin, medecin qui le suit, patient lui-même
     * Nous allons utiliser la policy pour gérer cela.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Patient::class);

        $patients = Patient::with(['user.profile'])
            ->when($request->input('search'), function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->input('groupe_sanguin'), function ($query, $groupe) {
                $query->where('groupe_sanguin', $groupe);
            })
            ->orderBy('id')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'data' => PatientResource::collection($patients),
            'meta' => [
                'total' => $patients->total(),
                'per_page' => $patients->perPage(),
                'current_page' => $patients->currentPage(),
            ],
        ]);
    }

    /**
     * Afficher les détails d'un patient.
     * Accessible par l'admin, le médecin qui le suit (via ses consultations) ou le patient lui-même.
     */
    public function show(Patient $patient)
    {
        $this->authorize('view', $patient);

        return response()->json([
            'data' => new PatientResource($patient->load(['user.profile', 'consultations.medecin.user', 'ordonnances.medicaments', 'dossiersMedicaux'])),
        ]);
    }

    /**
     * Créer un nouveau patient.
     * Seulement accessible par les admins (selon la spécification: POST/api/v1/patients -> admin).
     * Note: L'enregistrement d'un patient se fait normalement via l'inscription (AuthController) qui assigne le rôle patient.
     * Cependant, cette route permet de créer un enregistrement patient pour un utilisateur existant ayant le rôle patient.
     */
    public function store(StorePatientRequest $request)
    {
        $this->authorize('create', Patient::class);

        $patient = Patient::create($request->validated());

        return response()->json([
            'message' => 'Patient créé avec succès',
            'data' => new PatientResource($patient->load('user.profile')),
        ], 201);
    }

    /**
     * Mettre à jour un patient.
     * Accessible par l'admin ou le patient lui-même.
     */
    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $patient->update($request->validated());

        return response()->json([
            'message' => 'Patient mis à jour avec succès',
            'data' => new PatientResource($patient->load('user.profile')),
        ]);
    }

    /**
     * Supprimer un patient (soft delete).
     * Seulement accessible par les admins.
     */
    public function destroy(Patient $patient)
    {
        $this->authorize('delete', $patient);

        $patient->delete();

        return response()->json([
            'message' => 'Patient supprimé avec succès',
        ]);
    }

    /**
     * Afficher les consultations d'un patient.
     * Accessible par l'admin, le médecin qui le suit ou le patient lui-même.
     */
    public function consultations(Request $request, Patient $patient)
    {
        $this->authorize('view', $patient); // Using view policy as we are viewing the patient's data

        $consultations = $patient->consultations()
            ->with(['medecin.user.profile'])
            ->when($request->input('search'), function ($query, $search) {
                $query->whereHas('medecin.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->input('statut'), function ($query, $statut) {
                $query->where('statut', $statut);
            })
            ->when($request->input('date_debut'), function ($query, $date) {
                $query->whereDate('date_heure', '>=', $date);
            })
            ->when($request->input('date_fin'), function ($query, $date) {
                $query->whereDate('date_heure', '<=', $date);
            })
            ->orderBy('date_heure', 'desc')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'data' => ConsultationResource::collection($consultations),
            'meta' => [
                'total' => $consultations->total(),
                'per_page' => $consultations->perPage(),
                'current_page' => $consultations->currentPage(),
            ],
        ]);
    }

    /**
     * Afficher les dossiers médicaux d'un patient.
     * Accessible par l'admin, le médecin qui le suit (via les consultations) ou le patient lui-même.
     */
    public function dossiers(Request $request, Patient $patient)
    {
        $this->authorize('view', $patient);

        $dossiers = $patient->dossiersMedicaux()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('titre', 'like', "%{$search}%")
                      ->orWhere('contenu', 'like', "%{$search}%");
            })
            ->when($request->input('type'), function ($query, $type) {
                $query->where('type', $type);
            })
            ->orderBy('id')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'data' => DossierMedicalResource::collection($dossiers),
            'meta' => [
                'total' => $dossiers->total(),
                'per_page' => $dossiers->perPage(),
                'current_page' => $dossiers->currentPage(),
            ],
        ]);
    }

    /**
     * Afficher les ordonnances d'un patient.
     * Accessible par l'admin, le médecin qui le suit ou le patient lui-même.
     */
    public function ordonnances(Request $request, Patient $patient)
    {
        $this->authorize('view', $patient);

        $ordonnances = $patient->ordonnances()
            ->with(['consultation.medecin.user', 'medicaments'])
            ->when($request->input('search'), function ($query, $search) {
                $query->whereHas('consultation.medecin.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->input('statut'), function ($query, $statut) {
                $query->where('statut', $statut);
            })
            ->when($request->input('date_debut'), function ($query, $date) {
                $query->whereDate('date_emission', '>=', $date);
            })
            ->when($request->input('date_fin'), function ($query, $date) {
                $query->whereDate('date_emission', '<=', $date);
            })
            ->orderBy('id')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'data' => OrdonnanceResource::collection($ordonnances),
            'meta' => [
                'total' => $ordonnances->total(),
                'per_page' => $ordonnances->perPage(),
                'current_page' => $ordonnances->currentPage(),
            ],
        ]);
    }
}