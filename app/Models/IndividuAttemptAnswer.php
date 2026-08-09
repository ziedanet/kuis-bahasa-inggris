<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndividuAttemptAnswer extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $table = 'zd_individu_attempt_answers';

    protected $fillable = ['attempt_id', 'soal_id', 'opsi_id', 'is_correct', 'score'];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(IndividuAttempt::class, 'attempt_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(SoalIndividu::class, 'soal_id');
    }

    public function opsi(): BelongsTo
    {
        return $this->belongsTo(OpsiIndividu::class, 'opsi_id');
    }
}
