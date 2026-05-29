<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediAccessLog extends Model
{
    protected $fillable = [
        'medi_patient_id',
        'medi_doctor_id',
        'action',
        'ip_address',
    ];
}
