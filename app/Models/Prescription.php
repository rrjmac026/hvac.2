<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'user_id',
        'medical_record_id',
        'diagnosis_id',
        'medication_name',
        'dosage',
        'frequency',
        'duration_days',
        'instructions',
        'prescribed_at',
        'refills_remaining',
        'status',       // active, completed, cancelled
    ];

    protected $casts = [
        'prescribed_at'    => 'date',
        'refills_remaining' => 'integer',
    ];

    // Relationships
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function prescribedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function diagnosis()
    {
        return $this->belongsTo(Diagnosis::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'medication_name', 'name');
    }
}
