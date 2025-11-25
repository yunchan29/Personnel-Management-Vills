<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OtherFile;

class File201 extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sss_number',
        'philhealth_number',
        'pagibig_number',
        'tin_id_number',
        'licenses',
        'sss_file_path',
        'philhealth_file_path',
        'pagibig_file_path',
        'tin_file_path',
    ];

    protected $casts = [
        'licenses' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
