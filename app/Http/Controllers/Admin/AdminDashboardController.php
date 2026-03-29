<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InventoryItem;
use App\Models\Pet;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_clients'      => Client::count(),
            'total_pets'         => Pet::count(),
            'total_staff'        => User::where('is_active', true)->count(),
            'low_stock_items'    => InventoryItem::where('is_active', true)
                                        ->whereColumn('stock_quantity', '<=', 'reorder_threshold')
                                        ->count(),
        ];

        $todayAppointments = Appointment::with(['client', 'pet', 'assignedUser'])
            ->whereDate('scheduled_at', today())
            ->orderBy('scheduled_at')
            ->get();

        $recentInvoices = Invoice::with('client')
            ->latest('issued_at')
            ->limit(5)
            ->get();

        $lowStockItems = InventoryItem::with('supplier')
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'reorder_threshold')
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        // Revenue this month
        $monthlyRevenue = Invoice::whereMonth('issued_at', now()->month)
            ->whereYear('issued_at', now()->year)
            ->whereNotIn('status', [Invoice::STATUS_CANCELLED, Invoice::STATUS_DRAFT])
            ->sum('total');

        return view('admin.dashboard', compact(
            'stats',
            'todayAppointments',
            'recentInvoices',
            'lowStockItems',
            'monthlyRevenue',
        ));
    }
}
