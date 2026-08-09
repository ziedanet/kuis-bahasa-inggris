<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizGroupAnswer extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $table = 'zd_quiz_group_answers';

    protected $fillable = [
        'quiz_group_id',
        'quiz_image_id',
        'selected_word_ids',
        'correct_count',
        'score',
    ];

    protected $casts = [
        'selected_word_ids' => 'array',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(QuizGroup::class, 'quiz_group_id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(QuizImage::class, 'quiz_image_id');
    }
}
