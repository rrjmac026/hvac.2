<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = PurchaseOrder::with(['supplier', 'orderedBy'])
            ->when($request->search, fn ($q) =>
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', "%{$request->search}%"))
            )
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest('ordered_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.purchase-orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $items     = InventoryItem::where('is_active', true)->orderBy('name')->get();

        return view('admin.purchase-orders.create', compact('suppliers', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id'                    => ['required', 'exists:suppliers,id'],
            'ordered_at'                     => ['required', 'date'],
            'notes'                          => ['nullable', 'string'],
            'items'                          => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id'      => ['required', 'exists:inventory_items,id'],
            'items.*.quantity_ordered'       => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_cost'              => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $totalCost = collect($validated['items'])->sum(
                fn ($i) => $i['quantity_ordered'] * $i['unit_cost']
            );

            $order = PurchaseOrder::create([
                'supplier_id'  => $validated['supplier_id'],
                'user_id'      => auth()->id(),
                'order_number' => $this->generateOrderNumber(),
                'ordered_at'   => $validated['ordered_at'],
                'status'       => PurchaseOrder::STATUS_ORDERED,
                'total_cost'   => $totalCost,
                'notes'        => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $line) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'inventory_item_id' => $line['inventory_item_id'],
                    'quantity_ordered'  => $line['quantity_ordered'],
                    'quantity_received' => 0,
                    'unit_cost'         => $line['unit_cost'],
                    'subtotal'          => $line['quantity_ordered'] * $line['unit_cost'],
                ]);
            }
        });

        return redirect()->route('admin.purchase-orders.index')
            ->with('success', 'Purchase order created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'orderedBy', 'items.inventoryItem']);

        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === PurchaseOrder::STATUS_RECEIVED) {
            return back()->with('error', 'Cannot edit a completed purchase order.');
        }

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $items     = InventoryItem::where('is_active', true)->orderBy('name')->get();
        $purchaseOrder->load('items');

        return view('admin.purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'items'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === PurchaseOrder::STATUS_RECEIVED) {
            return back()->with('error', 'Cannot edit a completed purchase order.');
        }

        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'ordered_at'  => ['required', 'date'],
            'status'      => ['required', Rule::in([PurchaseOrder::STATUS_PENDING, PurchaseOrder::STATUS_ORDERED, PurchaseOrder::STATUS_CANCELLED])],
            'notes'       => ['nullable', 'string'],
        ]);

        $purchaseOrder->update($validated);

        return redirect()->route('admin.purchase-orders.index')
            ->with('success', 'Purchase order updated.');
    }

    /**
     * Mark a purchase order as received — updates stock levels.
     */
    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === PurchaseOrder::STATUS_RECEIVED) {
            return back()->with('error', 'This order has already been received.');
        }

        $validated = $request->validate([
            'items'                     => ['required', 'array'],
            'items.*.id'                => ['required', 'exists:purchase_order_items,id'],
            'items.*.quantity_received' => ['required', 'numeric', 'min:0'],
            'received_at'               => ['required', 'date'],
        ]);

        DB::transaction(function () use ($validated, $purchaseOrder) {
            foreach ($validated['items'] as $line) {
                $orderItem = PurchaseOrderItem::find($line['id']);
                $orderItem->update(['quantity_received' => $line['quantity_received']]);

                if ($line['quantity_received'] > 0) {
                    $inventoryItem = InventoryItem::lockForUpdate()->find($orderItem->inventory_item_id);
                    $before        = $inventoryItem->stock_quantity;
                    $after         = $before + $line['quantity_received'];

                    $inventoryItem->update(['stock_quantity' => $after]);

                    StockMovement::create([
                        'inventory_item_id' => $inventoryItem->id,
                        'user_id'           => auth()->id(),
                        'type'              => StockMovement::TYPE_IN,
                        'quantity'          => $line['quantity_received'],
                        'quantity_before'   => $before,
                        'quantity_after'    => $after,
                        'reference_type'    => PurchaseOrder::class,
                        'reference_id'      => $purchaseOrder->id,
                        'notes'             => "Received from PO #{$purchaseOrder->order_number}",
                    ]);
                }
            }

            $purchaseOrder->update([
                'status'      => PurchaseOrder::STATUS_RECEIVED,
                'received_at' => $validated['received_at'],
            ]);
        });

        return redirect()->route('admin.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase order marked as received and stock updated.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === PurchaseOrder::STATUS_RECEIVED) {
            return back()->with('error', 'Cannot delete a received purchase order.');
        }

        $purchaseOrder->update(['status' => PurchaseOrder::STATUS_CANCELLED]);

        return redirect()->route('admin.purchase-orders.index')
            ->with('success', 'Purchase order cancelled.');
    }

    private function generateOrderNumber(): string
    {
        $date     = now()->format('Ymd');
        $sequence = PurchaseOrder::whereDate('ordered_at', today())->count() + 1;

        return 'PO-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
