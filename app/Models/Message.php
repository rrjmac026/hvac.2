<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    const SENDER_TYPE_USER   = 'user';   // staff/vet
    const SENDER_TYPE_CLIENT = 'client';

    protected $fillable = [
        'client_id',
        'sender_type',   // 'user' or 'client'
        'sender_id',
        'body',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function sender()
    {
        return $this->morphTo();
    }
}
