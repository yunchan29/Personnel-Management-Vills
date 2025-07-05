<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'file_path',
    ];

    public function file201()
    {
        return $this->belongsTo(File201::class);
    }
}