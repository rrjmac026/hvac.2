<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'medical_record_id',
        'user_id',
        'diagnosed_at',
        'condition',
        'description',
        'treatment_plan',
        'follow_up_date',
        'status',       // active, resolved, chronic
    ];

    protected $casts = [
        'diagnosed_at'   => 'date',
        'follow_up_date' => 'date',
    ];

    // Relationships
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function vet()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
