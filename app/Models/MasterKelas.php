<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterKelas extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $table = 'zd_master_kelas';

    protected $fillable = ['name'];

    public function groups(): HasMany
    {
        return $this->hasMany(QuizGroup::class, 'kelas_id');
    }
}
