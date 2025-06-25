<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

   protected $fillable = [
    'job_title',
    'company_name',
    'job_industry',
    'role_type',
    'location',
    'vacancies',
    'apply_until',
    'qualifications',
    'additional_info',
];

    protected $casts = [
        'qualifications' => 'array',
        'additional_info' => 'array',
    ];

public function applications()
{
    return $this->hasMany(Application::class, 'job_id');
}

public function job()
{
    return $this->belongsTo(Job::class);
}

public function employees()
{
    return $this->hasMany(User::class);
}


}
