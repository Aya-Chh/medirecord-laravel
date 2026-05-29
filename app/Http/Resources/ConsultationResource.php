<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date_heure' => $this->date_heure,
            'motif' => $this->motif,
            'diagnostic' => $this->diagnostic,
            'notes' => $this->notes,
            'statut' => $this->statut,
            'duree_minutes' => $this->duree_minutes,
            'medecin' => new MedecinResource($this->whenLoaded('medecin')),
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'dossierMedical' => new DossierMedicalResource($this->whenLoaded('dossierMedical')),
            'ordonnances' => OrdonnanceResource::collection($this->whenLoaded('ordonnances')),
            'medicaments' => MedicamentResource::collection($this->whenLoaded('medicaments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}