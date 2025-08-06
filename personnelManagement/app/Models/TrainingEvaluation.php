<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'evaluated_by',
        'knowledge',
        'skill',
        'participation',
        'professionalism',
        'total_score',
        'result',
        'evaluated_at',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    public function evaluation()
{
    return $this->hasOne(\App\Models\TrainingEvaluation::class);
}

}
