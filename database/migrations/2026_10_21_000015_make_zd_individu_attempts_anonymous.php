<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kuis individu tidak lagi wajib login siswa: peserta mengisi nama + kelas langsung.
 * Jadi siswa_id boleh kosong, aturan unik (quiz_id, siswa_id) dilepas, dan nama + kelas
 * peserta disimpan di baris attempt itu sendiri. Data lama (yang punya siswa_id) tetap utuh.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Foreign key quiz_id & siswa_id memakai index unik lama, jadi dilepas dulu.
        Schema::table('zd_individu_attempts', function (Blueprint $table) {
            $table->dropForeign(['quiz_id']);
            $table->dropForeign(['siswa_id']);
        });

        Schema::table('zd_individu_attempts', function (Blueprint $table) {
            $table->dropUnique(['quiz_id', 'siswa_id']);
        });

        Schema::table('zd_individu_attempts', function (Blueprint $table) {
            $table->unsignedBigInteger('siswa_id')->nullable()->change();
            $table->string('participant_name', 150)->nullable()->after('siswa_id');
            $table->foreignId('kelas_id')->nullable()->after('participant_name')
                ->constrained('zd_master_kelas')->nullOnDelete();

            $table->foreign('quiz_id')->references('id')->on('zd_quizzes')->cascadeOnDelete();
            $table->foreign('siswa_id')->references('id')->on('zd_siswa')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('zd_individu_attempts', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn(['participant_name', 'kelas_id']);
        });
    }
};
