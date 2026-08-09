<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Master roster kelompok (BEDA dengan QuizGroup — QuizGroup itu sesi main per-kuis
 * yang dibuat on-the-fly, sedangkan ini akun kelompok yang didaftarkan admin
 * supaya bisa login (username + password) sebelum main.
 *
 * Extends Authenticatable supaya bisa dipakai guard "kelompok" di config/auth.php.
 */
class Kelompok extends Authenticatable
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $table = 'zd_kelompok';

    protected $fillable = ['name', 'kelas_id', 'username', 'password'];

    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(MasterKelas::class, 'kelas_id');
    }
}
