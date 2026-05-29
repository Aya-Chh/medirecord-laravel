<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medi_patients', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('cin')->unique();
            $table->date('birth_date');
            $table->string('name')->nullable();
            $table->string('session_token_hash')->nullable();
            $table->timestamp('welcome_sent_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        Schema::create('medi_doctors', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('profession');
            $table->string('specialty');
            $table->enum('sector', ['public', 'prive']);
            $table->string('professional_code_hash');
            $table->string('daily_code_hash');
            $table->timestamp('code_sent_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        Schema::create('medi_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medi_patient_id')->constrained('medi_patients')->cascadeOnDelete();
            $table->foreignId('medi_doctor_id')->constrained('medi_doctors')->cascadeOnDelete();
            $table->string('title')->default('Ordonnance');
            $table->longText('raw_text')->nullable();
            $table->longText('ai_text');
            $table->enum('status', ['draft', 'validated', 'cancelled'])->default('draft');
            $table->string('source_file_name')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('medi_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medi_patient_id')->nullable()->constrained('medi_patients')->nullOnDelete();
            $table->foreignId('medi_doctor_id')->nullable()->constrained('medi_doctors')->nullOnDelete();
            $table->string('action');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medi_access_logs');
        Schema::dropIfExists('medi_prescriptions');
        Schema::dropIfExists('medi_doctors');
        Schema::dropIfExists('medi_patients');
    }
};
