<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Medi\MediBotController;
use App\Http\Controllers\Api\Medi\MediDoctorController as MediMvpDoctorController;
use App\Http\Controllers\Api\Medi\MediPatientController as MediMvpPatientController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\MedecinController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\DossierMedicalController;
use App\Http\Controllers\Api\MedicamentController;
use App\Http\Controllers\Api\OrdonnanceController;

Route::prefix('medi')->group(function () {
    Route::post('patients/register', [MediMvpPatientController::class, 'register'])->middleware('throttle:8,1');
    Route::post('patients/login', [MediMvpPatientController::class, 'login'])->middleware('throttle:8,1');
    Route::get('patients/dashboard', [MediMvpPatientController::class, 'dashboard']);

    Route::post('doctors/register', [MediMvpDoctorController::class, 'register'])->middleware('throttle:8,1');
    Route::post('doctors/activate-code', [MediMvpDoctorController::class, 'activateLoginCode'])->middleware('throttle:8,1');
    Route::post('doctors/login', [MediMvpDoctorController::class, 'login'])->middleware('throttle:8,1');
    Route::post('doctors/find-patient', [MediMvpDoctorController::class, 'findPatient']);
    Route::post('doctors/extract-prescription', [MediMvpDoctorController::class, 'extractPrescription']);
    Route::post('doctors/prescriptions', [MediMvpDoctorController::class, 'storePrescription']);
    Route::get('doctors/patient-history', [MediMvpDoctorController::class, 'patientHistory']);

    Route::post('medibot/chat', [MediBotController::class, 'chat'])->middleware('throttle:20,1');
});

// -------------------- AUTH (public + protégé) --------------------
Route::prefix('auth')->group(function () {
    Route::post('register',        [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('login',           [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
    Route::post('reset-password',  [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout',  [AuthController::class, 'logout']);
        Route::get ('me',      [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

// -------------------- API v1 (auth requise) --------------------
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Users & profils
    Route::apiResource('users', UserController::class);
    Route::get ('users/{user}/profile', [ProfileController::class, 'show']);
    Route::put ('users/{user}/profile', [ProfileController::class, 'update']);

    // Médecins
    Route::apiResource('medecins', MedecinController::class);
    Route::get('medecins/{medecin}/consultations', [MedecinController::class, 'consultations']);
    Route::get('medecins/{medecin}/patients',      [MedecinController::class, 'patients']);

    // Patients
    Route::apiResource('patients', PatientController::class);
    Route::get('patients/{patient}/consultations', [PatientController::class, 'consultations']);
    Route::get('patients/{patient}/dossiers',      [PatientController::class, 'dossiers']);
    Route::get('patients/{patient}/ordonnances',   [PatientController::class, 'ordonnances']);

    // Consultations
    Route::apiResource('consultations', ConsultationController::class);
    Route::patch('consultations/{consultation}/statut', [ConsultationController::class, 'updateStatut']);

    // Dossiers médicaux (upload fichier sur POST/PUT)
    Route::apiResource('dossiers', DossierMedicalController::class);

    // Médicaments
    Route::apiResource('medicaments', MedicamentController::class);

    // Ordonnances + génération PDF
    Route::apiResource('ordonnances', OrdonnanceController::class);
    Route::get('ordonnances/{ordonnance}/pdf', [OrdonnanceController::class, 'pdf']);
});
