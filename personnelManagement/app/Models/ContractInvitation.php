<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'sent_by',
        'contract_date',
        'contract_signing_time',
        'email_sent',
        'sent_at',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'email_sent' => 'boolean',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the application that this invitation belongs to
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the user (HR staff) who sent this invitation
     */
    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
