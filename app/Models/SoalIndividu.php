<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoalIndividu extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $table = 'zd_soal_individu';

    protected $fillable = [
        'quiz_id',
        'narasi',
        'gambar',
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

    public function opsi(): HasMany
    {
        return $this->hasMany(OpsiIndividu::class, 'soal_id');
    }

    public function correctOpsi(): HasMany
    {
        return $this->opsi()->where('is_correct', true);
    }

    public function gambarUrl(): ?string
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }
}
