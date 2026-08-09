<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zd_quiz_group_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_group_id')->constrained('zd_quiz_groups')->cascadeOnDelete();
            $table->foreignId('quiz_image_id')->constrained('zd_quiz_images')->cascadeOnDelete();
            $table->json('selected_word_ids');
            $table->unsignedTinyInteger('correct_count')->default(0);
            $table->decimal('score', 5, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['quiz_group_id', 'quiz_image_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zd_quiz_group_answers');
    }
};
