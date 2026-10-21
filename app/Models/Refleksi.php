<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Refleksi peserta didik: 2 isian bebas + 3 penilaian bintang (1-5).
 * Diisi publik tanpa login (nama + kelas), dilihat guru di menu Refleksi.
 */
class Refleksi extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $table = 'zd_refleksi';

    /** Pernyataan penilaian: kolom => teks (sama persis dengan lembar refleksi). */
    public const STATEMENTS = [
        'rating_understand' => 'Saya memahami materi descriptive text hari ini.',
        'rating_teamwork' => 'Saya dapat bekerja sama dengan baik dalam kelompok.',
        'rating_fun' => 'Pembelajaran hari ini menyenangkan.',
    ];

    protected $fillable = [
        'participant_name', 'kelas_id', 'learned', 'confusing',
        'rating_understand', 'rating_teamwork', 'rating_fun',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(MasterKelas::class, 'kelas_id');
    }

    /** Rata-rata tiga bintang peserta ini (1-5). */
    public function averageRating(): float
    {
        return round(($this->rating_understand + $this->rating_teamwork + $this->rating_fun) / 3, 1);
    }
}
