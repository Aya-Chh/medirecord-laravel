<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediPatient extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'cin',
        'birth_date',
        'name',
        'session_token_hash',
        'welcome_sent_at',
        'last_login_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'welcome_sent_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    protected $hidden = [
        'session_token_hash',
    ];

    public function prescriptions()
    {
        return $this->hasMany(MediPrescription::class);
    }
}
