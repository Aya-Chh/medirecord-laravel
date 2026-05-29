<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediDoctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'profession',
        'specialty',
        'sector',
        'professional_code_hash',
        'daily_code_hash',
        'login_code_hash',
        'code_sent_at',
        'login_code_set_at',
        'last_login_at',
    ];

    protected $casts = [
        'code_sent_at' => 'datetime',
        'login_code_set_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    protected $hidden = [
        'professional_code_hash',
        'daily_code_hash',
        'login_code_hash',
    ];

    public function prescriptions()
    {
        return $this->hasMany(MediPrescription::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return trim("Dr. {$this->first_name} {$this->last_name}");
    }
}
