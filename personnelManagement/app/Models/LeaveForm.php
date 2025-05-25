<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveForm extends Model
{
    use HasFactory;

    // Add new fields to $fillable
    protected $fillable = [
        'user_id',
        'leave_type',
        'date_range',
        'about',
        'file_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
