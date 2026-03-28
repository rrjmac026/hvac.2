<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'medical_record_id',
        'user_id',
        'test_name',
        'test_date',
        'result_summary',
        'file_path',        // attachment/PDF of the lab result
        'notes',
    ];

    protected $casts = [
        'test_date' => 'date',
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

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
