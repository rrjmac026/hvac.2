<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    const METHOD_CASH    = 'cash';
    const METHOD_CARD    = 'card';
    const METHOD_GCASH   = 'gcash';
    const METHOD_MAYA    = 'maya';
    const METHOD_ONLINE  = 'online';

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_method',
        'reference_number',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
