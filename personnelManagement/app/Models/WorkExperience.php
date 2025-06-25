<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_title',
        'company_name',
        'start_date',
        'end_date',
        'job_industry',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
