<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'medecin_id',
        'patient_id',
        'date_heure',
        'motif',
        'diagnostic',
        'notes',
        'statut',
        'duree_minutes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_heure' => 'datetime',
    ];

    /**
     * Get the medecin that owns the consultation.
     */
    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    /**
     * Get the patient that owns the consultation.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the dossier medical for the consultation.
     */
    public function dossierMedical()
    {
        return $this->hasOne(DossierMedical::class);
    }

    /**
     * Get the ordonnances for the consultation.
     */
    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class);
    }

    /**
     * Get the medicaments for the consultation via ordonnances.
     */
    public function medicaments()
    {
        return $this->belongsToMany(Medicament::class, 'ordonnance_medicament')
                    ->withPivot(['dosage', 'frequence', 'duree', 'instructions'])
                    ->withTimestamps();
    }
}