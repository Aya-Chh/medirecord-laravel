<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicament extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nom',
        'description',
        'forme',
        'dosage_disponible',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'dosage_disponible' => 'array',
    ];

    /**
     * Get the ordonnances for the medicament.
     */
    public function ordonnances()
    {
        return $this->belongsToMany(Ordonnance::class, 'ordonnance_medicament')
                    ->withPivot(['dosage', 'frequence', 'duree', 'instructions'])
                    ->withTimestamps();
    }
}