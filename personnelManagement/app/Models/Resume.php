<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'resume',
    ];



    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function getResume(): ?string
    {
        return $this->resume
            ? asset("storage/{$this->resume}")
            : null;
    }
}