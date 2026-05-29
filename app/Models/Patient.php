<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'groupe_sanguin',
        'allergies',
        'antecedents',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'allergies' => 'array',
        'antecedents' => 'array',
    ];

    /**
     * Get the user that owns the patient.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the consultations for the patient.
     */
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Get the ordonnances for the patient.
     */
    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class);
    }

    /**
     * Get the dossiers medicaux for the patient.
     */
    public function dossiersMedicaux()
    {
        return $this->hasMany(DossierMedical::class);
    }
}