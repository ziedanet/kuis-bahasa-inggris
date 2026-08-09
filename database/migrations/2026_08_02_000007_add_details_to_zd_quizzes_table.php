<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zd_quizzes', function (Blueprint $table) {
            $table->text('narration')->nullable()->after('description');
            $table->unsignedInteger('duration_minutes')->nullable()->after('narration');
        });
    }

    public function down(): void
    {
        Schema::table('zd_quizzes', function (Blueprint $table) {
            $table->dropColumn(['narration', 'duration_minutes']);
        });
    }
};
