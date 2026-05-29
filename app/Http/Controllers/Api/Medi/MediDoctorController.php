<?php

namespace App\Http\Controllers\Api\Medi;

use App\Http\Controllers\Controller;
use App\Models\MediAccessLog;
use App\Models\MediDoctor;
use App\Models\MediPatient;
use App\Models\MediPrescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MediDoctorController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:medi_doctors,email',
            'first_name' => 'required|string|max:120',
            'last_name' => 'required|string|max:120',
            'profession' => 'required|string|max:160',
            'specialty' => 'required|string|max:160',
            'sector' => 'required|in:public,prive',
            'professional_code' => 'required|string|min:4|max:80',
        ]);

        $dailyCode = strtoupper(Str::random(8));

        $doctor = MediDoctor::create([
            'email' => $validated['email'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'profession' => $validated['profession'],
            'specialty' => $validated['specialty'],
            'sector' => $validated['sector'],
            'professional_code_hash' => Hash::make($validated['professional_code']),
            'daily_code_hash' => Hash::make($dailyCode),
            'code_sent_at' => now(),
        ]);

        return response()->json([
            'message' => 'Médecin inscrit avec succès',
            'data' => [
                'doctor' => $this->doctorPayload($doctor),
                'daily_code' => $dailyCode,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:80',
        ]);

        $doctor = MediDoctor::all()->first(
            fn (MediDoctor $doctor) => $doctor->login_code_hash
                && Hash::check($validated['code'], $doctor->login_code_hash)
        );

        if (! $doctor) {
            return response()->json([
                'message' => "Code confidentiel invalide. Si vous n'avez pas encore activé votre compte, utilisez le code reçu par email pour créer votre code personnel.",
            ], 401);
        }

        $doctor->forceFill(['last_login_at' => now()])->save();

        MediAccessLog::create([
            'medi_doctor_id' => $doctor->id,
            'action' => 'doctor_login',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'data' => [
                'doctor' => $this->doctorPayload($doctor),
            ],
        ]);
    }

    public function activateLoginCode(Request $request)
    {
        $validated = $request->validate([
            'email_code' => 'required|string|max:80',
            'new_code' => [
                'required',
                'string',
                'min:7',
                'max:80',
                'regex:/[!@#$%^&*(),.?":{}|<>_\-+=\[\]\/\\\\;]/',
            ],
        ], [
            'new_code.min' => 'Le nouveau code doit contenir au moins 7 caractères.',
            'new_code.regex' => 'Le nouveau code doit contenir au moins un caractère spécial.',
        ]);

        $doctor = MediDoctor::all()->first(
            fn (MediDoctor $doctor) => Hash::check($validated['email_code'], $doctor->daily_code_hash)
        );

        if (! $doctor) {
            return response()->json(['message' => 'Le code reçu par email est invalide.'], 401);
        }

        $doctor->forceFill([
            'login_code_hash' => Hash::make($validated['new_code']),
            'login_code_set_at' => now(),
        ])->save();

        MediAccessLog::create([
            'medi_doctor_id' => $doctor->id,
            'action' => 'doctor_login_code_activation',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Votre code personnel est enregistré. Vous pouvez maintenant vous connecter avec ce code uniquement.',
            'data' => [
                'doctor' => $this->doctorPayload($doctor),
            ],
        ]);
    }

    public function findPatient(Request $request)
    {
        $validated = $request->validate([
            'cin' => 'required|string|max:30',
            'birth_date' => 'required|date',
            'doctor_id' => 'required|exists:medi_doctors,id',
        ]);

        $patient = MediPatient::where('cin', $validated['cin'])
            ->whereDate('birth_date', $validated['birth_date'])
            ->first();

        if (! $patient) {
            return response()->json(['message' => 'Patient introuvable'], 404);
        }

        MediAccessLog::create([
            'medi_patient_id' => $patient->id,
            'medi_doctor_id' => $validated['doctor_id'],
            'action' => 'doctor_patient_lookup',
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['data' => ['patient' => $this->patientPayload($patient)]]);
    }

    public function extractPrescription(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:medi_doctors,id',
            'patient_id' => 'required|exists:medi_patients,id',
            'typed_text' => 'nullable|string|max:10000',
            'prescription_file' => 'nullable|file|max:10240',
        ]);

        $file = $request->file('prescription_file');

        if ($file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

            if (! in_array($extension, $allowedExtensions, true)) {
                return response()->json([
                    'message' => 'Le fichier doit être une image JPG, PNG, WEBP ou un PDF.',
                    'errors' => [
                        'prescription_file' => [
                            'Le fichier doit être une image JPG, PNG, WEBP ou un PDF.',
                        ],
                    ],
                ], 422);
            }
        }

        $fileName = $file?->getClientOriginalName();
        $inputText = trim((string) ($validated['typed_text'] ?? ''));

        $aiText = $this->buildPrescriptionText($inputText, $fileName);

        return response()->json([
            'data' => [
                'extracted_text' => $aiText,
                'source_file_name' => $fileName,
            ],
        ]);
    }

    public function storePrescription(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:medi_doctors,id',
            'patient_id' => 'required|exists:medi_patients,id',
            'title' => 'nullable|string|max:160',
            'raw_text' => 'nullable|string|max:10000',
            'ai_text' => 'required|string|max:20000',
            'source_file_name' => 'nullable|string|max:255',
            'status' => 'required|in:validated,cancelled',
        ]);

        if ($validated['status'] === 'cancelled') {
            return response()->json(['message' => 'Ordonnance annulée']);
        }

        $prescription = MediPrescription::create([
            'medi_patient_id' => $validated['patient_id'],
            'medi_doctor_id' => $validated['doctor_id'],
            'title' => $validated['title'] ?? 'Ordonnance',
            'raw_text' => $validated['raw_text'] ?? null,
            'ai_text' => $validated['ai_text'],
            'status' => 'validated',
            'source_file_name' => $validated['source_file_name'] ?? null,
            'validated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Ordonnance validée',
            'data' => ['prescription' => $this->prescriptionPayload($prescription->load('doctor'))],
        ], 201);
    }

    public function patientHistory(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:medi_doctors,id',
            'patient_id' => 'required|exists:medi_patients,id',
        ]);

        $prescriptions = MediPrescription::with('doctor')
            ->where('medi_patient_id', $validated['patient_id'])
            ->where('status', 'validated')
            ->latest('validated_at')
            ->get();

        MediAccessLog::create([
            'medi_patient_id' => $validated['patient_id'],
            'medi_doctor_id' => $validated['doctor_id'],
            'action' => 'doctor_history_access',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'data' => [
                'prescriptions' => $prescriptions->map(fn ($prescription) => $this->prescriptionPayload($prescription)),
            ],
        ]);
    }

    private function buildPrescriptionText(string $inputText, ?string $fileName): string
    {
        if ($inputText !== '') {
            return "Synthèse IA à valider par le médecin:\n\n".trim($inputText);
        }

        return "Synthèse IA à valider par le médecin:\n\n".
            "Un fichier ordonnance a été ajouté".($fileName ? " ({$fileName})" : '').".\n".
            "L'OCR automatique n'a pas reçu de texte exploitable dans ce MVP. Veuillez relire l'ordonnance scannée, saisir les médicaments, les doses et la durée, puis valider.";
    }

    private function doctorPayload(MediDoctor $doctor): array
    {
        return [
            'id' => $doctor->id,
            'email' => $doctor->email,
            'first_name' => $doctor->first_name,
            'last_name' => $doctor->last_name,
            'name' => $doctor->display_name,
            'profession' => $doctor->profession,
            'specialty' => $doctor->specialty,
            'sector' => $doctor->sector,
        ];
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

    private function prescriptionPayload(MediPrescription $prescription): array
    {
        return [
            'id' => $prescription->id,
            'title' => $prescription->title,
            'text' => $prescription->ai_text,
            'doctor' => $prescription->doctor?->display_name,
            'specialty' => $prescription->doctor?->specialty,
            'validated_at' => $prescription->validated_at?->toDateTimeString(),
        ];
    }
}
