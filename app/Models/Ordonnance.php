<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ordonnance extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'consultation_id',
        'medecin_id',
        'patient_id',
        'date_emission',
        'date_expiration',
        'instructions_generales',
        'statut',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_emission' => 'date',
        'date_expiration' => 'date',
        'instructions_generales' => 'string',
    ];

    /**
     * Get the consultation that owns the ordonnance.
     */
    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    /**
     * Get the medecin that owns the ordonnance.
     */
    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    /**
     * Get the patient that owns the ordonnance.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the medicaments for the ordonnance.
     */
    public function medicaments()
    {
        return $this->belongsToMany(Medicament::class, 'ordonnance_medicament')
                    ->withPivot(['dosage', 'frequence', 'duree', 'instructions'])
                    ->withTimestamps();
    }
}