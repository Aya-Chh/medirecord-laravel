<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrdonnanceResource extends JsonResource
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
            'date_emission' => $this->date_emission,
            'date_expiration' => $this->date_expiration,
            'instructions_generales' => $this->instructions_generales,
            'statut' => $this->statut,
            'consultation' => new ConsultationResource($this->whenLoaded('consultation')),
            'medecin' => new MedecinResource($this->whenLoaded('medecin')),
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'medicaments' => MedicamentResource::collection($this->whenLoaded('medicaments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}