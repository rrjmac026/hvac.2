<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    const TYPE_APPOINTMENT  = 'appointment';
    const TYPE_VACCINATION  = 'vaccination';
    const TYPE_FOLLOW_UP    = 'follow_up';
    const TYPE_REORDER      = 'reorder';

    const CHANNEL_SMS   = 'sms';
    const CHANNEL_EMAIL = 'email';

    const STATUS_PENDING = 'pending';
    const STATUS_SENT    = 'sent';
    const STATUS_FAILED  = 'failed';

    protected $fillable = [
        'client_id',
        'related_type',
        'related_id',
        'type',
        'channel',
        'message',
        'scheduled_at',
        'sent_at',
        'status',
        'failure_reason',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function related()
    {
        return $this->morphTo();
    }
}
