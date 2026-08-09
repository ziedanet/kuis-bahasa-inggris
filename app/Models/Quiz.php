<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $table = 'zd_quizzes';

    protected $fillable = [
        'title',
        'description',
        'narration',
        'duration_minutes',
        'icon',
        'icon_color',
        'category_label',
        'category_color',
        'meta_primary',
        'meta_secondary',
        'meta_tertiary',
        'is_playable',
        'play_route',
        'order_number',
        'is_active',
        'is_kelompok',
    ];

    protected $casts = [
        'is_playable' => 'boolean',
        'is_active' => 'boolean',
        'is_kelompok' => 'boolean',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(QuizImage::class, 'quiz_id');
    }

    public function soalIndividu(): HasMany
    {
        return $this->hasMany(SoalIndividu::class, 'quiz_id');
    }

    public function soalIndividuCount(): int
    {
        return $this->relationLoaded('soalIndividu')
            ? $this->soalIndividu->count()
            : $this->soal_individu_count ?? $this->soalIndividu()->count();
    }

    public function soalCount(): int
    {
        return $this->relationLoaded('images')
            ? $this->images->count()
            : $this->images_count ?? $this->images()->count();
    }

    /**
     * URL tujuan tombol "Mulai Kuis". Null kalau kuis belum bisa dimainkan
     * (nonaktif atau belum ada soal sama sekali). Dihitung otomatis dari data,
     * jadi admin tidak perlu toggle manual "bisa dimainkan". Berlaku buat
     * kuis kelompok maupun individu — keduanya sekarang punya alur main publik.
     */
    public function playUrl(): ?string
    {
        if (! $this->is_active) {
            return null;
        }

        if ($this->is_kelompok) {
            $soalCount = $this->relationLoaded('images')
                ? $this->images->count()
                : $this->images_count ?? $this->images()->count();

            return $soalCount >= 1 ? route('quiz.landing', $this) : null;
        }

        $soalCount = $this->soalIndividuCount();

        return $soalCount >= 1 ? route('quiz.individu.play', $this) : null;
    }
}
