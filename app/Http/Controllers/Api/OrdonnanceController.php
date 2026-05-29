<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ordonnance\StoreOrdonnanceRequest;
use App\Http\Requests\Ordonnance\UpdateOrdonnanceRequest;
use App\Http\Resources\OrdonnanceResource;
use App\Models\Ordonnance;
use App\Services\OrdonnancePdfService;
use Illuminate\Http\Request;

/**
 * Controller pour la gestion des ordonnances.
 */
class OrdonnanceController extends Controller
{
    /**
     * Afficher une liste paginée des ordonnances.
     * Accessible par:
     *   - admin: toutes les ordonnances
     *   - medecin: ses propres ordonnances émises
     *   - patient: ses propres ordonnances reçues
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Ordonnance::class);

        $user = $request->user();
        $query = Ordonnance::with(['medecin.user.profile', 'patient.user.profile', 'medicaments']);

        // Filter by user role
        if ($user->hasRole('medecin')) {
            $query->where('medecin_id', $user->medecin?->id);
        } elseif ($user->hasRole('patient')) {
            $query->where('patient_id', $user->patient?->id);
        }
        // Admin sees all

        // Apply additional filters
        $query->when($request->input('statut'), function ($q, $statut) {
            $q->where('statut', $statut);
        })
        ->when($request->input('patient_id'), function ($q, $patientId) {
            $q->where('patient_id', $patientId);
        })
        ->when($request->input('medecin_id'), function ($q, $medecinId) {
            $q->where('medecin_id', $medecinId);
        })
        ->when($request->input('date_debut'), function ($q, $date) {
            $q->whereDate('date_emission', '>=', $date);
        })
        ->when($request->input('date_fin'), function ($q, $date) {
            $q->whereDate('date_emission', '<=', $date);
        });

        $ordonnances = $query->orderBy('date_emission', 'desc')
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

    /**
     * Afficher les détails d'une ordonnance.
     * Accessible par l'admin, le médecin prescripteur ou le patient concerné.
     */
    public function show(Ordonnance $ordonnance)
    {
        $this->authorize('view', $ordonnance);

        return response()->json([
            'data' => new OrdonnanceResource($ordonnance->load([
                'medecin.user.profile',
                'patient.user.profile',
                'consultation',
                'medicaments'
            ])),
        ]);
    }

    /**
     * Créer une nouvelle ordonnance.
     * Accessible uniquement par les médecins et admins.
     */
    public function store(StoreOrdonnanceRequest $request)
    {
        $this->authorize('create', Ordonnance::class);

        $data = $request->validated();
        $medicaments = $data['medicaments'];
        unset($data['medicaments']);

        $ordonnance = Ordonnance::create($data);

        // Attach medicaments with pivot attributes
        foreach ($medicaments as $medicament) {
            $ordonnance->medicaments()->attach($medicament['id'], [
                'dosage' => $medicament['dosage'],
                'frequence' => $medicament['frequence'],
                'duree' => $medicament['duree'],
                'instructions' => $medicament['instructions'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Ordonnance créée avec succès',
            'data' => new OrdonnanceResource($ordonnance->load(['medecin.user.profile', 'patient.user.profile', 'medicaments'])),
        ], 201);
    }

    /**
     * Mettre à jour une ordonnance.
     * Accessible par l'admin ou le médecin prescripteur concerné.
     */
    public function update(UpdateOrdonnanceRequest $request, Ordonnance $ordonnance)
    {
        $this->authorize('update', $ordonnance);

        $data = $request->validated();
        $medicaments = $data['medicaments'] ?? null;
        unset($data['medicaments']);

        $ordonnance->update($data);

        // Sync medicaments if provided
        if (is_array($medicaments)) {
            $syncData = [];
            foreach ($medicaments as $medicament) {
                $syncData[$medicament['id']] = [
                    'dosage' => $medicament['dosage'],
                    'frequence' => $medicament['frequence'],
                    'duree' => $medicament['duree'],
                    'instructions' => $medicament['instructions'] ?? null,
                ];
            }
            $ordonnance->medicaments()->sync($syncData);
        }

        return response()->json([
            'message' => 'Ordonnance mise à jour avec succès',
            'data' => new OrdonnanceResource($ordonnance->fresh(['medecin.user.profile', 'patient.user.profile', 'medicaments'])),
        ]);
    }

    /**
     * Supprimer une ordonnance (soft delete).
     * Accessible uniquement par les admins.
     */
    public function destroy(Ordonnance $ordonnance)
    {
        $this->authorize('delete', $ordonnance);

        $ordonnance->delete();

        return response()->json([
            'message' => 'Ordonnance supprimée avec succès',
        ]);
    }

    /**
     * Générer et retourner le PDF de l'ordonnance.
     * Accessible par l'admin, le médecin prescripteur ou le patient concerné.
     */
    public function pdf(Ordonnance $ordonnance, OrdonnancePdfService $service)
    {
        $this->authorize('view', $ordonnance);

        $ordonnance->load([
            'medecin.user.profile',
            'patient.user.profile',
            'consultation',
            'medicaments'
        ]);

        return $service->generate($ordonnance);
    }
}
