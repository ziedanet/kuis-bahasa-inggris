<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpsiIndividu extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $table = 'zd_opsi_individu';

    protected $fillable = ['soal_id', 'opsi_text', 'is_correct'];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function soal(): BelongsTo
    {
        return $this->belongsTo(SoalIndividu::class, 'soal_id');
    }
}
