<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizImage extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $table = 'zd_quiz_images';

    protected $fillable = [
        'quiz_id',
        'session_type',
        'title',
        'instruction',
        'image_path',
        'order_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function words(): HasMany
    {
        return $this->hasMany(QuizWord::class);
    }

    public function correctWords(): HasMany
    {
        return $this->words()->where('is_correct', true);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizGroupAnswer::class);
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        // Gambar bawaan (seeder) ditaruh langsung sebagai file statis di public/images/...
        if (str_starts_with($this->image_path, 'images/')) {
            return asset($this->image_path);
        }

        // Gambar yang diupload admin lewat form soal disimpan di storage/app/public/...
        return asset('storage/' . $this->image_path);
    }
}
