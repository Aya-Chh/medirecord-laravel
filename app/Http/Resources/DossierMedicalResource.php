<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DossierMedicalResource extends JsonResource
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
            'titre' => $this->titre,
            'contenu' => $this->contenu,
            'type' => $this->type,
            'fichier_path' => $this->fichier_path ? asset('storage/' . $this->fichier_path) : null,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'consultation' => new ConsultationResource($this->whenLoaded('consultation')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}