<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DossierMedical\StoreDossierMedicalRequest;
use App\Http\Requests\DossierMedical\UpdateDossierMedicalRequest;
use App\Http\Resources\DossierMedicalResource;
use App\Models\DossierMedical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Controller pour la gestion des dossiers médicaux.
 */
class DossierMedicalController extends Controller
{
    /**
     * Afficher une liste paginée des dossiers médicaux.
     * Accessible par:
     *   - admin: tous les dossiers
     *   - medecin: dossiers des patients qu'il suit (via ses consultations)
     *   - patient: ses propres dossiers
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', DossierMedical::class);

        $query = DossierMedical::with(['patient.user.profile', 'consultation.medecin.user.profile']);

        // Filter by user role
        if ($request->user()->hasRole('medecin')) {
            // Medecin can see dossiers of patients they have consultations with
            $query->whereHas('patient.consultations.medecin', function ($q) use ($request) {
                $q->where('medecins.id', $request->user()->medecin->id);
            });
        } elseif ($request->user()->hasRole('patient')) {
            $query->where('patient_id', $request->user()->patient->id);
        }
        // Admin sees all, so no extra where clause

        // Apply additional filters
        $query->when($request->input('search'), function ($query, $search) {
            $query->where('titre', 'like', "%{$search}%")
                  ->orWhere('contenu', 'like', "%{$search}%");
        })
        ->when($request->input('type'), function ($query, $type) {
            $query->where('type', $type);
        })
        ->when($request->input('patient_id'), function ($query, $patientId) {
            $query->where('patient_id', $patientId);
        })
        ->when($request->input('consultation_id'), function ($query, $consultationId) {
            $query->where('consultation_id', $consultationId);
        })
        ->orderBy('id', 'desc');

        $dossiers = $query->paginate($request->input('per_page', 15));

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
     * Afficher les détails d'un dossier médical.
     * Accessible par:
     *   - admin: tout dossier
     *   - medecin: s'il est le médecin de la consultation liée au dossier (ou si le dossier appartient à un patient qu'il suit)
     *   - patient: s'il est le propriétaire du dossier
     */
    public function show(DossierMedical $dossier)
    {
        $this->authorize('view', $dossier);

        return response()->json([
            'data' => new DossierMedicalResource($dossier->load([
                'patient.user.profile',
                'consultation.medecin.user.profile',
            ])),
        ]);
    }

    /**
     * Créer un nouveau dossier médical.
     * Accessible par admin et medecin.
     */
    public function store(StoreDossierMedicalRequest $request)
    {
        $this->authorize('create', DossierMedical::class);

        $dossier = DossierMedical::create($request->validated());

        return response()->json([
            'message' => 'Dossier médical créé avec succès',
            'data' => new DossierMedicalResource($dossier->load(['patient.user.profile', 'consultation.medecin.user.profile'])),
        ], 201);
    }

    /**
     * Mettre à jour un dossier médical.
     * Accessible par:
     *   - admin: tout dossier
     *   - medecin: s'il est le médecin de la consultation liée au dossier (ou si le dossier appartient à un patient qu'il suit)
     *   - patient: s'il est le propriétaire du dossier
     */
    public function update(UpdateDossierMedicalRequest $request, DossierMedical $dossier)
    {
        $this->authorize('update', $dossier);

        $dossier->update($request->validated());

        return response()->json([
            'message' => 'Dossier médical mis à jour avec succès',
            'data' => new DossierMedicalResource($dossier->fresh()->load(['patient.user.profile', 'consultation.medecin.user.profile'])),
        ]);
    }

    /**
     * Supprimer un dossier médical (soft delete).
     * Accessible uniquement par admin.
     */
    public function destroy(DossierMedical $dossier)
    {
        $this->authorize('delete', $dossier);

        $dossier->delete();

        return response()->json([
            'message' => 'Dossier médical supprimé avec succès',
        ]);
    }

    /**
     * Télécharger le fichier associé au dossier médical.
     * Accessible par les mêmes rôles que la visualisation du dossier.
     */
    public function download(DossierMedical $dossier)
    {
        $this->authorize('view', $dossier);

        if (!$dossier->fichier_path || !Storage::disk('public')->exists($dossier->fichier_path)) {
            return response()->json([
                'message' => 'Fichier non trouvé',
            ], 404);
        }

        return Storage::disk('public')->download($dossier->fichier_path);
    }
}