<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zd_soal_individu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('zd_quizzes')->cascadeOnDelete();
            $table->text('narasi');
            $table->string('gambar')->nullable(); // opsional, tidak wajib
            $table->unsignedInteger('order_number')->default(1);
            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zd_soal_individu');
    }
};
