<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zd_refleksi', function (Blueprint $table) {
            $table->id();
            $table->string('participant_name', 150);
            $table->foreignId('kelas_id')->nullable()->constrained('zd_master_kelas')->nullOnDelete();

            // Dua isian bebas.
            $table->text('learned');
            $table->text('confusing')->nullable();

            // Tiga pernyataan, masing-masing bintang 1-5.
            $table->unsignedTinyInteger('rating_understand');
            $table->unsignedTinyInteger('rating_teamwork');
            $table->unsignedTinyInteger('rating_fun');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zd_refleksi');
    }
};
