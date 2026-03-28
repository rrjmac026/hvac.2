<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_DRAFT     = 'draft';
    const STATUS_ISSUED    = 'issued';
    const STATUS_PAID      = 'paid';
    const STATUS_PARTIALLY = 'partially_paid';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'client_id',
        'appointment_id',
        'invoice_number',
        'issued_at',
        'due_date',
        'subtotal',
        'discount',
        'tax',
        'total',
        'amount_paid',
        'status',
        'notes',
    ];

    protected $casts = [
        'issued_at'   => 'date',
        'due_date'    => 'date',
        'subtotal'    => 'decimal:2',
        'discount'    => 'decimal:2',
        'tax'         => 'decimal:2',
        'total'       => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Computed
    public function getBalanceAttribute(): float
    {
        return $this->total - $this->amount_paid;
    }
}
