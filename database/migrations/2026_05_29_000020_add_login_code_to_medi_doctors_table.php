<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medi_doctors', function (Blueprint $table) {
            $table->string('login_code_hash')->nullable()->after('daily_code_hash');
            $table->timestamp('login_code_set_at')->nullable()->after('code_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('medi_doctors', function (Blueprint $table) {
            $table->dropColumn(['login_code_hash', 'login_code_set_at']);
        });
    }
};
