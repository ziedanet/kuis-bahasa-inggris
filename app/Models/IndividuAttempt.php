<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndividuAttempt extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $table = 'zd_individu_attempts';

    protected $fillable = ['quiz_id', 'siswa_id', 'finished_at'];

    protected $casts = [
        'finished_at' => 'datetime',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(IndividuAttemptAnswer::class, 'attempt_id');
    }

    public function totalScore(): float
    {
        // Kalau sudah di-eager-load via withSum('answers', 'score'), pakai itu
        // (hindari query tambahan per baris). Kalau belum, query langsung.
        if ($this->answers_sum_score !== null) {
            return (float) $this->answers_sum_score;
        }

        return (float) $this->answers()->sum('score');
    }

    /**
     * Skor maksimum = jumlah soal individu di kuis ini (1 poin per soal benar).
     */
    public function maxScore(): float
    {
        return (float) ($this->quiz?->soalIndividuCount() ?? 0);
    }

    /**
     * Persentase capaian (0-100), dihitung dari totalScore/maxScore. Ini yang
     * jadi dasar predikat DAN dasar "skor" yang ditampilkan ke user — supaya
     * semua kuis (berapa pun jumlah soalnya) selalu setara di skala 0-100.
     */
    public function percent(): float
    {
        $max = $this->maxScore();

        return $max > 0 ? ($this->totalScore() / $max) * 100 : 0;
    }

    /**
     * Skor final yang ditampilkan ke user, selalu 0-100 apa pun jumlah soalnya.
     */
    public function scoreOn100(): float
    {
        return round($this->percent(), 1);
    }

    public function predikat(): string
    {
        $percent = $this->percent();

        return match (true) {
            $percent >= 90 => 'Sangat Baik',
            $percent >= 60 => 'Baik',
            $percent >= 30 => 'Cukup',
            default => 'Perlu Bimbingan',
        };
    }

    public function durationSeconds(): ?int
    {
        if (! $this->finished_at || ! $this->created_at) {
            return null;
        }

        return (int) $this->created_at->diffInSeconds($this->finished_at);
    }

    public function durationLabel(): string
    {
        $seconds = $this->durationSeconds();

        if ($seconds === null) {
            return '-';
        }

        $minutes = intdiv($seconds, 60);
        $secs = $seconds % 60;

        return $minutes > 0 ? "{$minutes} mnt {$secs} dtk" : "{$secs} dtk";
    }
}
