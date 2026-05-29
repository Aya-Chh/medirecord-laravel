<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MediMvpTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_doctor_prescription_flow(): void
    {
        $patient = $this->postJson('/api/medi/patients/register', [
            'email' => 'patient@example.com',
            'cin' => 'AB123456',
            'birth_date' => '1995-03-15',
            'name' => 'Patient Test',
        ])->assertCreated()->json('data.patient');

        $this->postJson('/api/medi/patients/login', [
            'cin' => 'AB123456',
            'birth_date' => '1995-03-15',
        ])->assertOk()->assertJsonPath('data.patient.id', $patient['id']);

        $doctorResponse = $this->postJson('/api/medi/doctors/register', [
            'email' => 'doctor@example.com',
            'first_name' => 'Sara',
            'last_name' => 'Alami',
            'profession' => 'Medecin',
            'specialty' => 'Cardiologie',
            'sector' => 'prive',
            'professional_code' => 'CARD-001',
        ])->assertCreated();

        $doctor = $doctorResponse->json('data.doctor');
        $dailyCode = $doctorResponse->json('data.daily_code');
        $personalCode = 'Medic@25';

        $this->postJson('/api/medi/doctors/activate-code', [
            'email_code' => $dailyCode,
            'new_code' => $personalCode,
        ])->assertOk();

        $this->postJson('/api/medi/doctors/login', [
            'code' => $dailyCode,
        ])->assertUnauthorized();

        $this->postJson('/api/medi/doctors/login', [
            'code' => $personalCode,
        ])->assertOk()->assertJsonPath('data.doctor.id', $doctor['id']);

        $this->postJson('/api/medi/doctors/activate-code', [
            'email_code' => $dailyCode,
            'new_code' => 'abcdefg',
        ])->assertUnprocessable();

        $this->postJson('/api/medi/doctors/login', [
            'code' => $dailyCode,
        ])->assertUnauthorized();

        $this->postJson('/api/medi/doctors/find-patient', [
            'doctor_id' => $doctor['id'],
            'cin' => 'AB123456',
            'birth_date' => '1995-03-15',
        ])->assertOk()->assertJsonPath('data.patient.id', $patient['id']);

        $extract = $this->postJson('/api/medi/doctors/extract-prescription', [
            'doctor_id' => $doctor['id'],
            'patient_id' => $patient['id'],
            'typed_text' => 'Paracetamol 500mg matin et soir pendant 3 jours.',
        ])->assertOk()->json('data.extracted_text');

        $this->post('/api/medi/doctors/extract-prescription', [
            'doctor_id' => $doctor['id'],
            'patient_id' => $patient['id'],
            'prescription_file' => UploadedFile::fake()->create('ordonnance.pdf', 120, 'application/pdf'),
        ])->assertOk()->assertJsonPath('data.source_file_name', 'ordonnance.pdf');

        $this->postJson('/api/medi/doctors/prescriptions', [
            'doctor_id' => $doctor['id'],
            'patient_id' => $patient['id'],
            'ai_text' => $extract,
            'status' => 'validated',
        ])->assertCreated();

        $this->getJson("/api/medi/doctors/patient-history?doctor_id={$doctor['id']}&patient_id={$patient['id']}")
            ->assertOk()
            ->assertJsonCount(1, 'data.prescriptions');
    }
}
