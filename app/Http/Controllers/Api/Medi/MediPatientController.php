<?php

namespace App\Http\Controllers\Api\Medi;

use App\Http\Controllers\Controller;
use App\Models\MediAccessLog;
use App\Models\MediPatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MediPatientController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:medi_patients,email',
            'cin' => 'required|string|max:30|unique:medi_patients,cin',
            'birth_date' => 'required|date',
            'name' => 'nullable|string|max:255',
        ]);

        $patient = MediPatient::create($validated);

        return response()->json([
            'message' => 'Patient inscrit avec succès',
            'data' => [
                'patient' => $this->patientPayload($patient),
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'cin' => 'required|string|max:30',
            'birth_date' => 'required|date',
        ]);

        $patient = MediPatient::where('cin', $validated['cin'])
            ->whereDate('birth_date', $validated['birth_date'])
            ->first();

        if (! $patient) {
            return response()->json(['message' => 'CIN ou date de naissance invalide'], 401);
        }

        $token = Str::random(64);
        $patient->forceFill([
            'session_token_hash' => Hash::make($token),
            'last_login_at' => now(),
        ])->save();

        MediAccessLog::create([
            'medi_patient_id' => $patient->id,
            'action' => 'patient_login',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'data' => [
                'patient' => $this->patientPayload($patient),
                'token' => $token,
            ],
        ]);
    }

    public function dashboard(Request $request)
    {
        $patient = MediPatient::with(['prescriptions' => function ($query) {
            $query->with('doctor')->where('status', 'validated')->latest();
        }])->findOrFail($request->integer('patient_id'));

        $latestDoctor = $patient->prescriptions->first()?->doctor;

        return response()->json([
            'data' => [
                'patient' => $this->patientPayload($patient),
                'doctor' => $latestDoctor ? [
                    'name' => $latestDoctor->display_name,
                    'specialty' => $latestDoctor->specialty,
                    'sector' => $latestDoctor->sector,
                ] : null,
                'prescriptions' => $patient->prescriptions->map(fn ($prescription) => [
                    'id' => $prescription->id,
                    'title' => $prescription->title,
                    'text' => $prescription->ai_text,
                    'doctor' => $prescription->doctor?->display_name,
                    'specialty' => $prescription->doctor?->specialty,
                    'validated_at' => $prescription->validated_at?->toDateTimeString(),
                ]),
            ],
        ]);
    }

    private function patientPayload(MediPatient $patient): array
    {
        return [
            'id' => $patient->id,
            'email' => $patient->email,
            'cin' => $patient->cin,
            'masked_cin' => substr($patient->cin, 0, 2).'****'.substr($patient->cin, -2),
            'birth_date' => $patient->birth_date?->toDateString(),
            'name' => $patient->name,
        ];
    }
}
