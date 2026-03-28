<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING    = 'pending';
    const STATUS_CONFIRMED  = 'confirmed';
    const STATUS_COMPLETED  = 'completed';
    const STATUS_CANCELLED  = 'cancelled';
    const STATUS_NO_SHOW    = 'no_show';

    protected $fillable = [
        'client_id',
        'pet_id',
        'user_id',       // assigned vet/staff
        'scheduled_at',
        'duration_minutes',
        'status',
        'reason',
        'notes',
        'is_online_booking',
    ];

    protected $casts = [
        'scheduled_at'    => 'datetime',
        'is_online_booking' => 'boolean',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function reminder()
    {
        return $this->hasOne(Reminder::class);
    }
}
