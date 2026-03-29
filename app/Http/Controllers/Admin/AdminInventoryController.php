<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminInventoryController extends Controller
{
    public function index(Request $request)
    {
        $items = InventoryItem::with('supplier')
            ->when($request->search, fn ($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%")
            )
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->low_stock, fn ($q) => $q->whereColumn('stock_quantity', '<=', 'reorder_threshold'))
            ->when($request->status, fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.inventory.index', compact('items'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('admin.inventory.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id'       => ['nullable', 'exists:suppliers,id'],
            'name'              => ['required', 'string', 'max:255'],
            'sku'               => ['nullable', 'string', 'max:100', 'unique:inventory_items,sku'],
            'type'              => ['required', Rule::in([
                                        InventoryItem::TYPE_MEDICATION,
                                        InventoryItem::TYPE_SUPPLY,
                                        InventoryItem::TYPE_SERVICE,
                                    ])],
            'description'       => ['nullable', 'string'],
            'unit'              => ['nullable', 'string', 'max:50'],
            'unit_price'        => ['required', 'numeric', 'min:0'],
            'selling_price'     => ['required', 'numeric', 'min:0'],
            'stock_quantity'    => ['required', 'numeric', 'min:0'],
            'reorder_threshold' => ['required', 'numeric', 'min:0'],
            'expiry_date'       => ['nullable', 'date', 'after:today'],
            'is_active'         => ['boolean'],
        ]);

        $item = InventoryItem::create($validated);

        // Log initial stock as an adjustment
        if ($validated['stock_quantity'] > 0) {
            StockMovement::create([
                'inventory_item_id' => $item->id,
                'user_id'           => auth()->id(),
                'type'              => StockMovement::TYPE_ADJ,
                'quantity'          => $validated['stock_quantity'],
                'quantity_before'   => 0,
                'quantity_after'    => $validated['stock_quantity'],
                'notes'             => 'Initial stock on item creation.',
            ]);
        }

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item created successfully.');
    }

    public function show(InventoryItem $inventory)
    {
        $inventory->load('supplier');

        $movements = StockMovement::where('inventory_item_id', $inventory->id)
            ->with('performedBy')
            ->latest()
            ->paginate(15);

        return view('admin.inventory.show', compact('inventory', 'movements'));
    }

    public function edit(InventoryItem $inventory)
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('admin.inventory.edit', compact('inventory', 'suppliers'));
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'supplier_id'       => ['nullable', 'exists:suppliers,id'],
            'name'              => ['required', 'string', 'max:255'],
            'sku'               => ['nullable', 'string', 'max:100', Rule::unique('inventory_items')->ignore($inventory->id)],
            'type'              => ['required', Rule::in([
                                        InventoryItem::TYPE_MEDICATION,
                                        InventoryItem::TYPE_SUPPLY,
                                        InventoryItem::TYPE_SERVICE,
                                    ])],
            'description'       => ['nullable', 'string'],
            'unit'              => ['nullable', 'string', 'max:50'],
            'unit_price'        => ['required', 'numeric', 'min:0'],
            'selling_price'     => ['required', 'numeric', 'min:0'],
            'reorder_threshold' => ['required', 'numeric', 'min:0'],
            'expiry_date'       => ['nullable', 'date'],
            'is_active'         => ['boolean'],
        ]);

        $inventory->update($validated);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(InventoryItem $inventory)
    {
        $inventory->delete(); // soft delete

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item removed.');
    }

    /**
     * Manual stock adjustment (add or subtract).
     */
    public function adjust(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'type'     => ['required', Rule::in([StockMovement::TYPE_IN, StockMovement::TYPE_OUT, StockMovement::TYPE_ADJ])],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'notes'    => ['required', 'string', 'max:255'],
        ]);

        $before = $inventory->stock_quantity;

        $after = match ($validated['type']) {
            StockMovement::TYPE_IN  => $before + $validated['quantity'],
            StockMovement::TYPE_OUT => $before - $validated['quantity'],
            StockMovement::TYPE_ADJ => $validated['quantity'], // set absolute value
        };

        if ($after < 0) {
            return back()->with('error', 'Stock cannot go below zero.');
        }

        $inventory->update(['stock_quantity' => $after]);

        StockMovement::create([
            'inventory_item_id' => $inventory->id,
            'user_id'           => auth()->id(),
            'type'              => $validated['type'],
            'quantity'          => $validated['quantity'],
            'quantity_before'   => $before,
            'quantity_after'    => $after,
            'notes'             => $validated['notes'],
        ]);

        return back()->with('success', 'Stock adjusted successfully.');
    }
}
