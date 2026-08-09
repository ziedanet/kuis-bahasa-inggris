<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zd_individu_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('zd_quizzes')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('zd_siswa')->cascadeOnDelete();
            $table->timestamp('finished_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['quiz_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zd_individu_attempts');
    }
};
