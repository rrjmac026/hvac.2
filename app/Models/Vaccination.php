<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaccination extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'user_id',
        'vaccine_name',
        'batch_number',
        'administered_at',
        'next_due_date',
        'notes',
    ];

    protected $casts = [
        'administered_at' => 'date',
        'next_due_date'   => 'date',
    ];

    // Relationships
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function administeredBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reminder()
    {
        return $this->hasOne(Reminder::class, 'related_id')
                    ->where('related_type', self::class);
    }
}
