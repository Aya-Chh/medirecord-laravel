<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medicament\StoreMedicamentRequest;
use App\Http\Requests\Medicament\UpdateMedicamentRequest;
use App\Http\Resources\MedicamentResource;
use App\Models\Medicament;
use Illuminate\Http\Request;

/**
 * Controller pour la gestion des médicaments.
 */
class MedicamentController extends Controller
{
    /**
     * Afficher une liste paginée des médicaments.
     * Accessible par tous les utilisateurs authentifiés.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Medicament::class);

        $medicaments = Medicament::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->input('forme'), function ($query, $forme) {
                $query->where('forme', $forme);
            })
            ->orderBy('id')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'data' => MedicamentResource::collection($medicaments),
            'meta' => [
                'total' => $medicaments->total(),
                'per_page' => $medicaments->perPage(),
                'current_page' => $medicaments->currentPage(),
            ],
        ]);
    }

    /**
     * Afficher les détails d'un médicament.
     * Accessible par tous les utilisateurs authentifiés.
     */
    public function show(Medicament $medicament)
    {
        $this->authorize('view', $medicament);

        return response()->json([
            'data' => new MedicamentResource($medicament->load('ordonnances.consultation')),
        ]);
    }

    /**
     * Créer un nouveau médicament.
     * Seulement accessible par les admins.
     */
    public function store(StoreMedicamentRequest $request)
    {
        $this->authorize('create', Medicament::class);

        $medicament = Medicament::create($request->validated());

        return response()->json([
            'message' => 'Médicament créé avec succès',
            'data' => new MedicamentResource($medicament),
        ], 201);
    }

    /**
     * Mettre à jour un médicament.
     * Seulement accessible par les admins.
     */
    public function update(UpdateMedicamentRequest $request, Medicament $medicament)
    {
        $this->authorize('update', $medicament);

        $medicament->update($request->validated());

        return response()->json([
            'message' => 'Médicament mis à jour avec succès',
            'data' => new MedicamentResource($medicament),
        ]);
    }

    /**
     * Supprimer un médicament.
     * Seulement accessible par les admins.
     */
    public function destroy(Medicament $medicament)
    {
        $this->authorize('delete', $medicament);

        $medicament->delete();

        return response()->json([
            'message' => 'Médicament supprimé avec succès',
        ]);
    }
}