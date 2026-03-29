@php
    $currentRoute = Route::currentRouteName();
    $is = fn(string $pattern) => Str::startsWith($currentRoute, $pattern) || $currentRoute === $pattern;
@endphp

{{-- ── Overview ── --}}
<div class="nav-section-label">Overview</div>

<a href="{{ route('admin.dashboard') }}"
   class="nav-link {{ $is('admin.dashboard') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-gauge-high"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Dashboard</span>
        <span class="nav-link-sub">System overview</span>
    </div>
</a>

{{-- ── Clinic Operations ── --}}
<div class="nav-section-label">Clinic Operations</div>

<a href="{{ route('clinic.appointments.index') }}"
   class="nav-link {{ $is('clinic.appointments') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-calendar-check"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Appointments</span>
        <span class="nav-link-sub">Schedule & manage visits</span>
    </div>
</a>

<a href="{{ route('clinic.clients.index') }}"
   class="nav-link {{ $is('clinic.clients') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-users"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Clients</span>
        <span class="nav-link-sub">Pet owner records</span>
    </div>
</a>

<a href="{{ route('clinic.online-bookings.index') }}"
   class="nav-link {{ $is('clinic.online-bookings') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-calendar-plus"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Online Bookings</span>
        <span class="nav-link-sub">Approve or reject requests</span>
    </div>
</a>

{{-- ── Medical ── --}}
<div class="nav-section-label">Medical</div>

<a href="{{ route('vet.medical-records.index') }}"
   class="nav-link {{ $is('vet.medical-records') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-notes-medical"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Medical Records</span>
        <span class="nav-link-sub">Visit notes & history</span>
    </div>
</a>

<a href="{{ route('vet.prescriptions.index') }}"
   class="nav-link {{ $is('vet.prescriptions') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-pills"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Prescriptions</span>
        <span class="nav-link-sub">Medications & refills</span>
    </div>
</a>

<a href="{{ route('vet.vaccinations.index') }}"
   class="nav-link {{ $is('vet.vaccinations') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-syringe"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Vaccinations</span>
        <span class="nav-link-sub">Vaccine schedules</span>
    </div>
</a>

<a href="{{ route('vet.lab-results.index') }}"
   class="nav-link {{ $is('vet.lab-results') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-flask"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Lab Results</span>
        <span class="nav-link-sub">Diagnostics & tests</span>
    </div>
</a>

{{-- ── Billing ── --}}
<div class="nav-section-label">Billing</div>

<a href="{{ route('pos.index') }}"
   class="nav-link {{ $is('pos') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-cash-register"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Point of Sale</span>
        <span class="nav-link-sub">Checkout & payments</span>
    </div>
</a>

<a href="{{ route('clinic.invoices.index') }}"
   class="nav-link {{ $is('clinic.invoices') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-file-invoice-dollar"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Invoices</span>
        <span class="nav-link-sub">Billing records</span>
    </div>
</a>

{{-- ── Inventory ── --}}
<div class="nav-section-label">Inventory</div>

<a href="{{ route('admin.inventory.index') }}"
   class="nav-link {{ $is('admin.inventory') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-box-open"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Inventory</span>
        <span class="nav-link-sub">Stock & supplies</span>
    </div>
    @php
        $lowStockCount = \App\Models\InventoryItem::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'reorder_threshold')->count();
    @endphp
    @if($lowStockCount > 0)
        <span class="nav-badge orange">{{ $lowStockCount }}</span>
    @endif
</a>

<a href="{{ route('admin.suppliers.index') }}"
   class="nav-link {{ $is('admin.suppliers') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-truck"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Suppliers</span>
        <span class="nav-link-sub">Vendor management</span>
    </div>
</a>

<a href="{{ route('admin.purchase-orders.index') }}"
   class="nav-link {{ $is('admin.purchase-orders') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-cart-flatbed"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Purchase Orders</span>
        <span class="nav-link-sub">Restock & receiving</span>
    </div>
</a>

{{-- ── Reports ── --}}
<div class="nav-section-label">Reports</div>

<a href="{{ route('admin.reports.financial') }}"
   class="nav-link {{ $currentRoute === 'admin.reports.financial' ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-chart-line"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Financial</span>
        <span class="nav-link-sub">Revenue & payments</span>
    </div>
</a>

<a href="{{ route('admin.reports.appointments') }}"
   class="nav-link {{ $currentRoute === 'admin.reports.appointments' ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-chart-bar"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Appointments</span>
        <span class="nav-link-sub">Visit trends & stats</span>
    </div>
</a>

<a href="{{ route('admin.reports.inventory') }}"
   class="nav-link {{ $currentRoute === 'admin.reports.inventory' ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-chart-pie"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Inventory Report</span>
        <span class="nav-link-sub">Stock movements</span>
    </div>
</a>

<hr class="sidebar-divider">

{{-- ── Communication ── --}}
<div class="nav-section-label">Communication</div>

<a href="{{ route('clinic.messages.index') }}"
   class="nav-link {{ $is('clinic.messages') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-comment-medical"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Messages</span>
        <span class="nav-link-sub">Client communications</span>
    </div>
</a>

<a href="{{ route('clinic.reminders.index') }}"
   class="nav-link {{ $is('clinic.reminders') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-bell"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Reminders</span>
        <span class="nav-link-sub">SMS & email alerts</span>
    </div>
</a>

<hr class="sidebar-divider">

{{-- ── System ── --}}
<div class="nav-section-label">System</div>

<a href="{{ route('admin.users.index') }}"
   class="nav-link {{ $is('admin.users') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-user-gear"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Staff Accounts</span>
        <span class="nav-link-sub">Manage users & roles</span>
    </div>
</a>

<a href="{{ route('admin.audit-logs.index') }}"
   class="nav-link {{ $is('admin.audit-logs') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-clock-rotate-left"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Audit Logs</span>
        <span class="nav-link-sub">Activity history</span>
    </div>
    <span class="nav-badge green" style="background:linear-gradient(135deg,rgba(168,232,0,0.15),rgba(197,245,0,0.1));color:#A8E800;border:1px solid rgba(168,232,0,0.25);box-shadow:none;">Live</span>
</a>

<hr class="sidebar-divider">

{{-- ── Account ── --}}
<div class="nav-section-label">Account</div>

<a href="{{ route('profile.edit') }}"
   class="nav-link {{ $is('profile') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-circle-user"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">My Profile</span>
        <span class="nav-link-sub">Account settings</span>
    </div>
</a>