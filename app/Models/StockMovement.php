<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    const TYPE_IN  = 'in';   // received / restocked
    const TYPE_OUT = 'out';  // dispensed / sold
    const TYPE_ADJ = 'adjustment'; // manual correction

    protected $fillable = [
        'inventory_item_id',
        'user_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reference_type',   // e.g. App\Models\PurchaseOrder
        'reference_id',
        'notes',
    ];

    protected $casts = [
        'quantity'        => 'decimal:2',
        'quantity_before' => 'decimal:2',
        'quantity_after'  => 'decimal:2',
    ];

    // Relationships
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
