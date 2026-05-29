<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consultation\StoreConsultationRequest;
use App\Http\Requests\Consultation\UpdateConsultationRequest;
use App\Http\Resources\ConsultationResource;
use App\Models\Consultation;
use Illuminate\Http\Request;

/**
 * Controller pour la gestion des consultations.
 */
class ConsultationController extends Controller
{
    /**
     * Afficher une liste paginée des consultations.
     * Accessible par:
     *   - admin: toutes les consultations
     *   - medecin: ses propres consultations
     *   - patient: ses propres consultations
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Consultation::class);

        $query = Consultation::with(['medecin.user.profile', 'patient.user.profile']);

        // Filter by user role
        if ($request->user()->hasRole('medecin')) {
            $query->where('medecin_id', $request->user()->medecin->id);
        } elseif ($request->user()->hasRole('patient')) {
            $query->where('patient_id', $request->user()->patient->id);
        }
        // Admin sees all, so no extra where clause

        // Apply additional filters
        $query->when($request->input('search'), function ($query, $search) {
            $query->whereHas('medecin.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('patient.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        })
        ->when($request->input('statut'), function ($query, $statut) {
            $query->where('statut', $statut);
        })
        ->when($request->input('medecin_id'), function ($query, $medecinId) {
            $query->where('medecin_id', $medecinId);
        })
        ->when($request->input('patient_id'), function ($query, $patientId) {
            $query->where('patient_id', $patientId);
        })
        ->when($request->input('date_debut'), function ($query, $date) {
            $query->whereDate('date_heure', '>=', $date);
        })
        ->when($request->input('date_fin'), function ($query, $date) {
            $query->whereDate('date_heure', '<=', $date);
        })
        ->orderBy('date_heure', 'desc');

        $consultations = $query->paginate($request->input('per_page', 15));

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
     * Afficher les détails d'une consultation.
     * Accessible par:
     *   - admin: toute consultation
     *   - medecin: s'il est le médecin de la consultation
     *   - patient: s'il est le patient de la consultation
     */
    public function show(Consultation $consultation)
    {
        $this->authorize('view', $consultation);

        return response()->json([
            'data' => new ConsultationResource($consultation->load([
                'medecin.user.profile',
                'patient.user.profile',
                'dossierMedical',
                'ordonnances.medicaments',
                'medicaments'
            ])),
        ]);
    }

    /**
     * Créer une nouvelle consultation.
     * Accessible par admin et medecin.
     */
    public function store(StoreConsultationRequest $request)
    {
        $this->authorize('create', Consultation::class);

        $consultation = Consultation::create($request->validated());

        return response()->json([
            'message' => 'Consultation créée avec succès',
            'data' => new ConsultationResource($consultation->load(['medecin.user.profile', 'patient.user.profile'])),
        ], 201);
    }

    /**
     * Mettre à jour une consultation.
     * Accessible par admin et medecin concerné.
     */
    public function update(UpdateConsultationRequest $request, Consultation $consultation)
    {
        $this->authorize('update', $consultation);

        $consultation->update($request->validated());

        return response()->json([
            'message' => 'Consultation mise à jour avec succès',
            'data' => new ConsultationResource($consultation->load(['medecin.user.profile', 'patient.user.profile'])),
        ]);
    }

    /**
     * Supprimer une consultation (soft delete).
     * Accessible uniquement par admin.
     */
    public function destroy(Consultation $consultation)
    {
        $this->authorize('delete', $consultation);

        $consultation->delete();

        return response()->json([
            'message' => 'Consultation supprimée avec succès',
        ]);
    }

    /**
     * Mettre à jour le statut d'une consultation.
     * Accessible par admin et medecin concerné.
     */
    public function updateStatut(Request $request, Consultation $consultation)
    {
        $this->authorize('update', $consultation); // Reuse the update policy for simplicity, or create a specific policy method if needed

        $request->validate([
            'statut' => 'required|in:planifiee,en_cours,terminee,annulee',
        ]);

        $consultation->update(['statut' => $request->input('statut')]);

        return response()->json([
            'message' => 'Statut de la consultation mis à jour avec succès',
            'data' => new ConsultationResource($consultation->load(['medecin.user.profile', 'patient.user.profile'])),
        ]);
    }
}