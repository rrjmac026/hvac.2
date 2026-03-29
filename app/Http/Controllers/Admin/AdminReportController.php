<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Pet;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    /**
     * Financial report — revenue, payments, and expenses summary.
     */
    public function financial(Request $request)
    {
        $from = $request->date('from', 'Y-m-d') ?? now()->startOfMonth();
        $to   = $request->date('to', 'Y-m-d')   ?? now()->endOfMonth();

        $invoices = Invoice::with(['client', 'payments'])
            ->whereBetween('issued_at', [$from, $to])
            ->whereNotIn('status', [Invoice::STATUS_DRAFT, Invoice::STATUS_CANCELLED])
            ->orderBy('issued_at')
            ->get();

        $summary = [
            'gross_sales'     => $invoices->sum('total'),
            'total_collected' => $invoices->sum('amount_paid'),
            'total_balance'   => $invoices->sum(fn ($i) => $i->total - $i->amount_paid),
            'total_discount'  => $invoices->sum('discount'),
            'invoice_count'   => $invoices->count(),
        ];

        // Revenue by payment method
        $byPaymentMethod = Payment::whereBetween('paid_at', [$from, $to])
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        // Daily revenue chart data
        $dailyRevenue = Invoice::whereBetween('issued_at', [$from, $to])
            ->whereNotIn('status', [Invoice::STATUS_DRAFT, Invoice::STATUS_CANCELLED])
            ->selectRaw('DATE(issued_at) as date, SUM(total) as revenue, COUNT(*) as transactions')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top selling items
        $topItems = InvoiceItem::whereHas('invoice', fn ($q) =>
                $q->whereBetween('issued_at', [$from, $to])
                  ->whereNotIn('status', [Invoice::STATUS_CANCELLED])
            )
            ->selectRaw('description, SUM(quantity) as total_qty, SUM(subtotal) as total_sales')
            ->groupBy('description')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        return view('admin.reports.financial', compact(
            'invoices', 'summary', 'byPaymentMethod', 'dailyRevenue', 'topItems', 'from', 'to'
        ));
    }

    /**
     * Appointment report — trends, completion rates, no-shows.
     */
    public function appointments(Request $request)
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to   = $request->date('to')   ?? now()->endOfMonth();

        $appointments = Appointment::with(['client', 'pet', 'assignedUser'])
            ->whereBetween('scheduled_at', [$from, $to])
            ->orderBy('scheduled_at')
            ->get();

        $summary = [
            'total'     => $appointments->count(),
            'completed' => $appointments->where('status', Appointment::STATUS_COMPLETED)->count(),
            'cancelled' => $appointments->where('status', Appointment::STATUS_CANCELLED)->count(),
            'no_show'   => $appointments->where('status', Appointment::STATUS_NO_SHOW)->count(),
            'pending'   => $appointments->where('status', Appointment::STATUS_PENDING)->count(),
        ];

        // Appointments per day
        $daily = Appointment::whereBetween('scheduled_at', [$from, $to])
            ->selectRaw('DATE(scheduled_at) as date, COUNT(*) as count, status')
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        // Busiest hours
        $byHour = Appointment::whereBetween('scheduled_at', [$from, $to])
            ->selectRaw('HOUR(scheduled_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return view('admin.reports.appointments', compact(
            'appointments', 'summary', 'daily', 'byHour', 'from', 'to'
        ));
    }

    /**
     * Inventory report — stock levels, movements, low stock, expiring items.
     */
    public function inventory(Request $request)
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to   = $request->date('to')   ?? now()->endOfMonth();

        // Low stock items
        $lowStock = \App\Models\InventoryItem::with('supplier')
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'reorder_threshold')
            ->orderBy('stock_quantity')
            ->get();

        // Expiring items (within 90 days)
        $expiring = \App\Models\InventoryItem::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays(90))
            ->orderBy('expiry_date')
            ->get();

        // Stock movements in range
        $movements = \App\Models\StockMovement::with(['inventoryItem', 'performedBy'])
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->paginate(20);

        // Top consumed items
        $topConsumed = \App\Models\StockMovement::where('type', \App\Models\StockMovement::TYPE_OUT)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('inventory_item_id, SUM(quantity) as total_consumed')
            ->groupBy('inventory_item_id')
            ->with('inventoryItem')
            ->orderByDesc('total_consumed')
            ->limit(10)
            ->get();

        return view('admin.reports.inventory', compact(
            'lowStock', 'expiring', 'movements', 'topConsumed', 'from', 'to'
        ));
    }

    /**
     * Client & pet statistics.
     */
    public function clients(Request $request)
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to   = $request->date('to')   ?? now()->endOfMonth();

        $newClients = Client::whereBetween('created_at', [$from, $to])->count();
        $newPets    = Pet::whereBetween('created_at', [$from, $to])->count();

        $petsBySpecies = Pet::selectRaw('species, COUNT(*) as count')
            ->groupBy('species')
            ->orderByDesc('count')
            ->get();

        // Clients with most visits
        $topClients = Client::withCount(['appointments' => fn ($q) =>
                $q->whereBetween('scheduled_at', [$from, $to])
                  ->where('status', Appointment::STATUS_COMPLETED)
            ])
            ->orderByDesc('appointments_count')
            ->limit(10)
            ->get();

        return view('admin.reports.clients', compact(
            'newClients', 'newPets', 'petsBySpecies', 'topClients', 'from', 'to'
        ));
    }
}
