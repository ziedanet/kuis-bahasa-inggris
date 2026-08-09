<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizGroup extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $table = 'zd_quiz_groups';

    protected $fillable = ['quiz_id', 'name', 'class', 'kelas_id', 'finished_at', 'kelompok_id'];

    protected $casts = [
        'finished_at' => 'datetime',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(MasterKelas::class, 'kelas_id');
    }

    public function kelompokAccount(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizGroupAnswer::class);
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
     * Skor maksimum = jumlah soal kuis ini x 12.5 (2.5 poin/kata benar x 5 kata).
     * Dulu di-hardcode /50 (asumsi selalu 4 soal) — sekarang dihitung dinamis
     * supaya kuis lain yang jumlah soalnya beda tetap valid.
     */
    public function maxScore(): float
    {
        $soalCount = $this->quiz?->soalCount() ?? 0;

        return $soalCount * 12.5;
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

    /**
     * Lama waktu pengerjaan (detik), dihitung dari kapan kelompok dibuat (mulai kuis)
     * sampai finished_at (waktu soal terakhir dijawab). Null kalau belum selesai.
     */
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
