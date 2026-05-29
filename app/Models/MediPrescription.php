<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediPrescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'medi_patient_id',
        'medi_doctor_id',
        'title',
        'raw_text',
        'ai_text',
        'status',
        'source_file_name',
        'validated_at',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(MediPatient::class, 'medi_patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(MediDoctor::class, 'medi_doctor_id');
    }
}
