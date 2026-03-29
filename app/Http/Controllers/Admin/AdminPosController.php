<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Services\PosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPosController extends Controller
{
    public function __construct(protected PosService $posService) {}

    /**
     * Main POS screen.
     * Loads all active sellable items (medications, supplies, services).
     */
    public function index()
    {
        $items = InventoryItem::query()
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->groupBy('type'); // groups into 'medication', 'supply', 'service'

        $clients = Client::orderBy('last_name')->get(['id', 'first_name', 'last_name']);

        return view('pos.index', compact('items', 'clients'));
    }

    /**
     * Quick item search (for search bar in POS screen).
     */
    public function searchItems(Request $request)
    {
        $query = $request->string('q');

        $items = InventoryItem::query()
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get(['id', 'name', 'sku', 'type', 'selling_price', 'stock_quantity', 'unit']);

        return response()->json($items);
    }

    /**
     * Checkout — creates Invoice, InvoiceItems, Payment, and StockMovements atomically.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'client_id'              => ['nullable', 'exists:clients,id'],
            'appointment_id'         => ['nullable', 'exists:appointments,id'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id' => ['nullable', 'exists:inventory_items,id'],
            'items.*.description'    => ['required', 'string', 'max:255'],
            'items.*.quantity'       => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price'     => ['required', 'numeric', 'min:0'],
            'discount'               => ['nullable', 'numeric', 'min:0'],
            'tax'                    => ['nullable', 'numeric', 'min:0'],
            'payment_method'         => ['required', 'in:cash,card,gcash,maya,online'],
            'amount_tendered'        => ['required_if:payment_method,cash', 'nullable', 'numeric', 'min:0'],
            'reference_number'       => ['nullable', 'string', 'max:100'],
            'notes'                  => ['nullable', 'string'],
        ]);

        try {
            $result = DB::transaction(function () use ($validated) {
                return $this->posService->processCheckout($validated);
            });

            return response()->json([
                'success'        => true,
                'invoice'        => $result['invoice']->load('items', 'payments', 'client'),
                'change'         => $result['change'],
                'invoice_number' => $result['invoice']->invoice_number,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Show a single invoice / receipt (for printing).
     */
    public function receipt(Invoice $invoice)
    {
        $invoice->load(['client', 'items.inventoryItem', 'payments', 'appointment.pet']);

        return view('pos.receipt', compact('invoice'));
    }

    /**
     * Void / cancel an invoice.
     * Reverses stock movements tied to this invoice.
     */
    public function void(Request $request, Invoice $invoice)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        if ($invoice->status === Invoice::STATUS_CANCELLED) {
            return response()->json(['message' => 'Invoice is already cancelled.'], 422);
        }

        DB::transaction(function () use ($invoice, $request) {
            $this->posService->voidInvoice($invoice, $request->reason);
        });

        return response()->json(['success' => true]);
    }

    /**
     * Today's sales summary (for the POS dashboard strip).
     */
    public function summary()
    {
        $today = now()->toDateString();

        $summary = Invoice::query()
            ->whereDate('issued_at', $today)
            ->whereNotIn('status', [Invoice::STATUS_CANCELLED, Invoice::STATUS_DRAFT])
            ->selectRaw('COUNT(*) as transaction_count, SUM(total) as gross_sales, SUM(amount_paid) as collected')
            ->first();

        $topItems = InvoiceItem::query()
            ->whereHas('invoice', fn ($q) => $q->whereDate('issued_at', $today)
                ->whereNotIn('status', [Invoice::STATUS_CANCELLED]))
            ->selectRaw('description, SUM(quantity) as total_qty, SUM(subtotal) as total_sales')
            ->groupBy('description')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        return response()->json(compact('summary', 'topItems'));
    }
}
