<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zd_individu_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('zd_individu_attempts')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('zd_soal_individu')->cascadeOnDelete();
            $table->foreignId('opsi_id')->constrained('zd_opsi_individu')->cascadeOnDelete();
            $table->boolean('is_correct')->default(false);
            $table->decimal('score', 5, 2)->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['attempt_id', 'soal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zd_individu_attempt_answers');
    }
};
