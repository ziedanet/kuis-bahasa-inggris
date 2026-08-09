<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Extends Authenticatable (bukan Model biasa) supaya bisa login lewat guard "siswa"
 * (nisn + password) yang dikonfigurasi di config/auth.php.
 */
class Siswa extends Authenticatable
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $table = 'zd_siswa';

    protected $fillable = ['name', 'nisn', 'password', 'jenis_kelamin', 'kelas_id', 'foto'];

    protected $hidden = ['password'];

    protected $casts = [
        'jenis_kelamin' => 'boolean',
        'password' => 'hashed',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(MasterKelas::class, 'kelas_id');
    }

    public function jenisKelaminLabel(): string
    {
        return $this->jenis_kelamin ? 'Laki-Laki' : 'Perempuan';
    }

    public function fotoUrl(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }
}
