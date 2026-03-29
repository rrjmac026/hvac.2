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
        <span class="nav-link-sub">Your schedule today</span>
    </div>
</a>

{{-- ── Appointments ── --}}
<div class="nav-section-label">Schedule</div>

<a href="{{ route('clinic.appointments.index') }}"
   class="nav-link {{ $is('clinic.appointments') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-calendar-check"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Appointments</span>
        <span class="nav-link-sub">Today's visits</span>
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

<a href="{{ route('vet.diagnoses.index') }}"
   class="nav-link {{ $is('vet.diagnoses') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-stethoscope"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Diagnoses</span>
        <span class="nav-link-sub">Conditions & treatment plans</span>
    </div>
</a>

<a href="{{ route('vet.prescriptions.index') }}"
   class="nav-link {{ $is('vet.prescriptions') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-pills"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Prescriptions</span>
        <span class="nav-link-sub">Medications & dosages</span>
    </div>
</a>

<a href="{{ route('vet.vaccinations.index') }}"
   class="nav-link {{ $is('vet.vaccinations') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-syringe"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Vaccinations</span>
        <span class="nav-link-sub">Vaccine tracking</span>
    </div>
</a>

<a href="{{ route('vet.lab-results.index') }}"
   class="nav-link {{ $is('vet.lab-results') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-flask"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Lab Results</span>
        <span class="nav-link-sub">Test results & files</span>
    </div>
</a>

<hr class="sidebar-divider">

{{-- ── Patients ── --}}
<div class="nav-section-label">Patients</div>

<a href="{{ route('clinic.clients.index') }}"
   class="nav-link {{ $is('clinic.clients') ? 'active' : '' }}">
    <div class="nav-link-icon"><i class="fas fa-paw"></i></div>
    <div class="nav-link-text">
        <span class="nav-link-label">Clients & Pets</span>
        <span class="nav-link-sub">Patient records</span>
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