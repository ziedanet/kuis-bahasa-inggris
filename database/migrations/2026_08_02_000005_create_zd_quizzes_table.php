<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zd_quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->default('fa-solid fa-gamepad');
            $table->string('icon_color')->default('--primary-yellow');
            $table->string('category_label')->nullable();
            $table->string('category_color')->default('--primary-cyan');
            $table->string('meta_primary')->nullable();
            $table->string('meta_secondary')->nullable();
            $table->string('meta_tertiary')->nullable();
            $table->boolean('is_playable')->default(false);
            $table->string('play_route')->nullable();
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
        Schema::dropIfExists('zd_quizzes');
    }
};
