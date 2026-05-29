<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medecin extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'specialite',
        'numero_ordre',
        'hopital',
        'bio',
        'disponible',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'disponible' => 'boolean',
    ];

    /**
     * Get the user that owns the medecin.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the consultations for the medecin.
     */
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Get the ordonnances for the medecin.
     */
    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class);
    }

    /**
     * Get the dossiers medicaux for the medecin via consultations.
     */
    public function dossiersMedicaux()
    {
        return $this->hasManyThrough(DossierMedical::class, Consultation::class);
    }

    /**
     * Get distinct patients followed by this medecin (via consultations).
     */
    public function patients()
    {
        return Patient::whereIn('id', $this->consultations()->pluck('patient_id'));
    }

    /**
     * Scope a query to only include available medecins.
     */
    public function scopeDisponible($query)
    {
        return $query->where('disponible', true);
    }
}