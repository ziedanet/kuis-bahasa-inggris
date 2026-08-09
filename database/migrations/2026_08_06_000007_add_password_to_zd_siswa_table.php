<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zd_siswa', function (Blueprint $table) {
            // Buat login siswa: nisn + password.
            $table->string('password')->nullable()->after('nisn');
        });
    }

    public function down(): void
    {
        Schema::table('zd_siswa', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
};
