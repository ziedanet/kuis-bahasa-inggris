<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zd_quiz_images', function (Blueprint $table) {
            $table->string('lokasi', 150)->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('zd_quiz_images', function (Blueprint $table) {
            $table->dropColumn('lokasi');
        });
    }
};
