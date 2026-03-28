<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_MEDICATION = 'medication';
    const TYPE_SUPPLY     = 'supply';
    const TYPE_SERVICE    = 'service';

    protected $fillable = [
        'supplier_id',
        'name',
        'sku',
        'type',
        'description',
        'unit',
        'unit_price',
        'selling_price',
        'stock_quantity',
        'reorder_threshold',
        'expiry_date',
        'is_active',
    ];

    protected $casts = [
        'unit_price'        => 'decimal:2',
        'selling_price'     => 'decimal:2',
        'stock_quantity'    => 'decimal:2',
        'reorder_threshold' => 'decimal:2',
        'expiry_date'       => 'date',
        'is_active'         => 'boolean',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Helpers
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->reorder_threshold;
    }
}
