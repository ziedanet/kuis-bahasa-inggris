<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zd_quiz_groups', function (Blueprint $table) {
            $table->foreignId('quiz_id')->nullable()->after('id')->constrained('zd_quizzes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('zd_quiz_groups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('quiz_id');
        });
    }
};
