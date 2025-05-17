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
}
