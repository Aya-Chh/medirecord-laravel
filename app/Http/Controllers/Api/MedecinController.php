<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medecin\StoreMedecinRequest;
use App\Http\Requests\Medecin\UpdateMedecinRequest;
use App\Http\Resources\MedecinResource;
use App\Http\Resources\ConsultationResource;
use App\Http\Resources\PatientResource;
use App\Models\Medecin;
use Illuminate\Http\Request;

/**
 * Controller pour la gestion des médecins.
 */
class MedecinController extends Controller
{
    /**
     * Afficher une liste paginée des médecins.
     * Accessible par tous les utilisateurs authentifiés.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Medecin::class);

        $medecins = Medecin::with(['user.profile'])
            ->when($request->input('search'), function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->input('specialite'), function ($query, $specialite) {
                $query->where('specialite', $specialite);
            })
            ->when($request->input('disponible'), function ($query, $disponible) {
                $query->where('disponible', $disponible);
            })
            ->orderBy('id')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'data' => MedecinResource::collection($medecins),
            'meta' => [
                'total' => $medecins->total(),
                'per_page' => $medecins->perPage(),
                'current_page' => $medecins->currentPage(),
            ],
        ]);
    }

    /**
     * Afficher les détails d'un médecin.
     * Accessible par tous les utilisateurs authentifiés.
     */
    public function show(Medecin $medecin)
    {
        $this->authorize('view', $medecin);

        return response()->json([
            'data' => new MedecinResource($medecin->load(['user.profile', 'consultations.patient', 'ordonnances.medicaments'])),
        ]);
    }

    /**
     * Créer un nouveau médecin.
     * Seulement accessible par les admins.
     */
    public function store(StoreMedecinRequest $request)
    {
        $this->authorize('create', Medecin::class);

        $medecin = Medecin::create($request->validated());

        return response()->json([
            'message' => 'Médecin créé avec succès',
            'data' => new MedecinResource($medecin->load('user.profile')),
        ], 201);
    }

    /**
     * Mettre à jour un médecin.
     * Accessible par l'admin ou le médecin lui-même.
     */
    public function update(UpdateMedecinRequest $request, Medecin $medecin)
    {
        $this->authorize('update', $medecin);

        $medecin->update($request->validated());

        return response()->json([
            'message' => 'Médecin mis à jour avec succès',
            'data' => new MedecinResource($medecin->load('user.profile')),
        ]);
    }

    /**
     * Supprimer un médecin.
     * Seulement accessible par les admins.
     */
    public function destroy(Medecin $medecin)
    {
        $this->authorize('delete', $medecin);

        $medecin->delete();

        return response()->json([
            'message' => 'Médecin supprimé avec succès',
        ]);
    }

    /**
     * Afficher les consultations d'un médecin.
     * Accessible par l'admin ou le médecin lui-même.
     */
    public function consultations(Request $request, Medecin $medecin)
    {
        $this->authorize('view', $medecin); // Using view policy as we are viewing the medecin's data

        $consultations = $medecin->consultations()
            ->with(['patient.user.profile'])
            ->when($request->input('search'), function ($query, $search) {
                $query->whereHas('patient.user', function ($q) use ($search) {
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
     * Afficher les patients d'un médecin (via ses consultations).
     * Accessible par l'admin ou le médecin lui-même.
     */
    public function patients(Request $request, Medecin $medecin)
    {
        $this->authorize('view', $medecin);

        // Get unique patients from the medic's consultations
        $patients = $medecin->patients()
            ->with('user.profile')
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
}