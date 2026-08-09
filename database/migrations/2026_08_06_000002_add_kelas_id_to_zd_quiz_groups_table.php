<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zd_quiz_groups', function (Blueprint $table) {
            // Ganti input kelas manual (kolom lama "class") jadi pilihan dari master kelas.
            $table->foreignId('kelas_id')->nullable()->after('class')
                ->constrained('zd_master_kelas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('zd_quiz_groups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kelas_id');
        });
    }
};
