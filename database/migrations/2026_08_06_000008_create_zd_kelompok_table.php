<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zd_kelompok', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // nama kelompok
            $table->foreignId('kelas_id')->nullable()->constrained('zd_master_kelas')->nullOnDelete();
            $table->string('username')->unique();
            $table->string('password'); // buat login kelompok (username + password)

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zd_kelompok');
    }
};
