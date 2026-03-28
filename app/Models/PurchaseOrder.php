<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    const STATUS_PENDING   = 'pending';
    const STATUS_ORDERED   = 'ordered';
    const STATUS_RECEIVED  = 'received';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'supplier_id',
        'user_id',
        'order_number',
        'ordered_at',
        'received_at',
        'status',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'ordered_at'  => 'date',
        'received_at' => 'date',
        'total_cost'  => 'decimal:2',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function orderedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
