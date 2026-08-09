<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizWord extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $table = 'zd_quiz_words';

    protected $fillable = ['quiz_image_id', 'word', 'is_correct'];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function image(): BelongsTo
    {
        return $this->belongsTo(QuizImage::class, 'quiz_image_id');
    }
}
