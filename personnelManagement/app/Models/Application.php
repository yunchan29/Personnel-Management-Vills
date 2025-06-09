<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'job_id',
        'resume_id',
        'licenses',
        'sss_number',
        'philhealth_number',
        'tin_id_number',
        'pagibig_number',
    ];

    protected $casts = [
        'licenses' => 'array', // Automatically handle JSON <-> array
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
