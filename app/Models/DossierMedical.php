<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DossierMedical extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'patient_id',
        'consultation_id',
        'titre',
        'contenu',
        'type',
        'fichier_path',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'contenu' => 'string',
    ];

    /**
     * Get the patient that owns the dossier medical.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the consultation that owns the dossier medical.
     */
    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}