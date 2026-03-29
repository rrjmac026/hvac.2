@php
    $currentRoute = Route::currentRouteName();
    $is = fn(string $pattern) => Str::startsWith($currentRoute, $pattern) || $currentRoute === $pattern;
@endphp

{{-- ── Overview ── --}}
<div class="nav-section-label">Overview</div>

<a href="{{ route('dashboard') }}"
   class="nav-link {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-gauge-high"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Dashboard</span>
        <span class="nav-link-sub">Today at a glance</span>
    </div>
</a>

{{-- ── Front Desk ── --}}
<div class="nav-section-label">Front Desk</div>

<a href="{{ route('clinic.appointments.index') }}"
   class="nav-link {{ $is('clinic.appointments') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-calendar-check"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Appointments</span>
        <span class="nav-link-sub">Schedule & walk-ins</span>
    </div>
</a>

<a href="{{ route('clinic.online-bookings.index') }}"
   class="nav-link {{ $is('clinic.online-bookings') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-calendar-plus"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Online Bookings</span>
        <span class="nav-link-sub">Pending approvals</span>
    </div>
</a>

<a href="{{ route('clinic.clients.index') }}"
   class="nav-link {{ $is('clinic.clients') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-users"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Clients</span>
        <span class="nav-link-sub">Owner & pet records</span>
    </div>
</a>

{{-- ── Billing ── --}}
<div class="nav-section-label">Billing</div>

<a href="{{ route('pos.index') }}"
   class="nav-link {{ $is('pos') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-cash-register"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Point of Sale</span>
        <span class="nav-link-sub">Process payments</span>
    </div>
</a>

<a href="{{ route('clinic.invoices.index') }}"
   class="nav-link {{ $is('clinic.invoices') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-file-invoice-dollar"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Invoices</span>
        <span class="nav-link-sub">Billing history</span>
    </div>
</a>

{{-- ── Communication ── --}}
<div class="nav-section-label">Communication</div>

<a href="{{ route('clinic.messages.index') }}"
   class="nav-link {{ $is('clinic.messages') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-comment-medical"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Messages</span>
        <span class="nav-link-sub">Chat with clients</span>
    </div>
</a>

<a href="{{ route('clinic.reminders.index') }}"
   class="nav-link {{ $is('clinic.reminders') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-bell"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Reminders</span>
        <span class="nav-link-sub">Upcoming notifications</span>
    </div>
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