@php
    $role = auth()->user()->role ?? 'staff';
@endphp

<style>
    /* ══════════════════════════════════════════
       SIDEBAR — Highland Vets · Forest/Lime
       ══════════════════════════════════════════ */

    /* ── Backdrop ── */
    .sidebar-backdrop {
        position: fixed; inset: 0; z-index: 40;
        background: rgba(10, 22, 12, 0.65);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        animation: backdrop-in 0.2s ease;
    }
    @keyframes backdrop-in { from { opacity: 0; } to { opacity: 1; } }

    html:not(.dark) .sidebar-backdrop { background: rgba(27, 67, 50, 0.25); }

    /* ── Sidebar shell ── */
    .sidebar-shell {
        position: fixed; top: 0; left: 0; bottom: 0;
        width: 18rem;
        z-index: 45;
        display: flex; flex-direction: column;
        background: rgba(10, 22, 12, 0.97);
        backdrop-filter: blur(24px) saturate(1.6);
        -webkit-backdrop-filter: blur(24px) saturate(1.6);
        border-right: 1px solid rgba(168, 232, 0, 0.14);
        box-shadow: 4px 0 40px rgba(0, 0, 0, 0.45), 0 0 0 0.5px rgba(168, 232, 0, 0.06);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                    background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
    }

    html:not(.dark) .sidebar-shell {
        background: rgba(255, 255, 255, 0.99);
        border-right-color: rgba(27, 67, 50, 0.15);
        box-shadow: 4px 0 40px rgba(27, 67, 50, 0.08), 0 0 0 0.5px rgba(27, 67, 50, 0.06);
    }

    /* ── Top accent line ── */
    .sidebar-shell::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, #1B4332 0%, #A8E800 50%, rgba(168,232,0,0.4) 100%);
        z-index: 1;
    }

    /* ── Sidebar header ── */
    .sidebar-header {
        height: 64px;
        display: flex; align-items: center;
        padding: 0 20px;
        border-bottom: 1px solid rgba(168, 232, 0, 0.1);
        flex-shrink: 0;
        background: linear-gradient(180deg, rgba(27, 67, 50, 0.5) 0%, transparent 100%);
        transition: background 0.3s, border-color 0.3s;
    }

    html:not(.dark) .sidebar-header {
        background: linear-gradient(180deg, rgba(245, 248, 240, 0.8) 0%, transparent 100%);
        border-bottom-color: rgba(27, 67, 50, 0.1);
    }

    .sidebar-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .sidebar-brand-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: linear-gradient(135deg, #A8E800, #C5F500);
        box-shadow: 0 0 8px rgba(168, 232, 0, 0.6);
        animation: dot-pulse 2.5s ease-in-out infinite;
    }
    @keyframes dot-pulse {
        0%, 100% { box-shadow: 0 0 6px rgba(168,232,0,0.4); transform: scale(1); }
        50%       { box-shadow: 0 0 16px rgba(168,232,0,0.9); transform: scale(1.18); }
    }
    .sidebar-brand-label {
        font-size: 0.78rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
        background: linear-gradient(105deg, #A8E800 0%, #C5F500 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }

    html:not(.dark) .sidebar-brand-label {
        background: linear-gradient(105deg, #1B4332 0%, #2D6A4F 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }

    /* ── Nav scroll area ── */
    .sidebar-nav {
        flex: 1; overflow-y: auto;
        padding: 14px 12px;
        scrollbar-width: thin;
        scrollbar-color: rgba(168,232,0,0.12) transparent;
    }
    .sidebar-nav::-webkit-scrollbar { width: 4px; }
    .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(168,232,0,0.12); border-radius: 99px; }

    html:not(.dark) .sidebar-nav { scrollbar-color: rgba(27,67,50,0.12) transparent; }
    html:not(.dark) .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(27,67,50,0.12); }

    /* ── Section label ── */
    .nav-section-label {
        font-size: 0.63rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;
        color: rgba(168, 232, 0, 0.45); padding: 12px 10px 6px;
        display: flex; align-items: center; gap: 6px;
        transition: color 0.3s;
    }
    .nav-section-label::after {
        content: ''; flex: 1; height: 1px;
        background: linear-gradient(90deg, rgba(168,232,0,0.18) 0%, transparent 100%);
        transition: background 0.3s;
    }

    html:not(.dark) .nav-section-label { color: rgba(27, 67, 50, 0.5); }
    html:not(.dark) .nav-section-label::after { background: linear-gradient(90deg, rgba(27,67,50,0.18) 0%, transparent 100%); }

    /* ── Nav link ── */
    .nav-link {
        display: flex; align-items: center; gap: 11px;
        padding: 9px 12px; border-radius: 10px;
        text-decoration: none; cursor: pointer;
        position: relative; overflow: hidden;
        transition: background 0.18s, transform 0.15s, box-shadow 0.18s, border-color 0.18s;
        margin-bottom: 2px;
        border: 1px solid transparent;
    }
    .nav-link:hover {
        background: rgba(168, 232, 0, 0.06);
        transform: translateX(3px);
        border-color: rgba(168, 232, 0, 0.12);
    }

    html:not(.dark) .nav-link:hover {
        background: rgba(27, 67, 50, 0.06);
        border-color: rgba(27, 67, 50, 0.12);
    }

    /* ── Active state ── */
    .nav-link.active {
        background: linear-gradient(105deg, rgba(168,232,0,0.11) 0%, rgba(168,232,0,0.05) 100%);
        border-color: rgba(168, 232, 0, 0.2);
        box-shadow: 0 2px 12px rgba(168, 232, 0, 0.08), inset 0 0 0 0.5px rgba(168,232,0,0.08);
    }
    .nav-link.active::before {
        content: '';
        position: absolute; left: 0; top: 20%; bottom: 20%;
        width: 3px; border-radius: 0 3px 3px 0;
        background: linear-gradient(180deg, #A8E800, #C5F500);
    }

    html:not(.dark) .nav-link.active {
        background: linear-gradient(105deg, rgba(27,67,50,0.1) 0%, rgba(27,67,50,0.05) 100%);
        border-color: rgba(27, 67, 50, 0.22);
        box-shadow: 0 2px 12px rgba(27, 67, 50, 0.08), inset 0 0 0 0.5px rgba(27,67,50,0.08);
    }
    html:not(.dark) .nav-link.active::before {
        background: linear-gradient(180deg, #1B4332, #2D6A4F);
    }

    /* ── Nav icon ── */
    .nav-link-icon {
        width: 32px; height: 32px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.82rem; flex-shrink: 0;
        background: rgba(168, 232, 0, 0.06);
        color: rgba(168, 232, 0, 0.65);
        border: 1px solid rgba(168, 232, 0, 0.1);
        transition: background 0.18s, color 0.18s, transform 0.18s, border-color 0.18s;
    }
    .nav-link:hover .nav-link-icon {
        background: rgba(168, 232, 0, 0.12);
        transform: scale(1.08);
        color: #A8E800;
    }
    .nav-link.active .nav-link-icon {
        background: linear-gradient(135deg, #1B4332, #2D6A4F);
        color: #A8E800;
        border-color: rgba(168,232,0,0.3);
        box-shadow: 0 3px 12px rgba(27, 67, 50, 0.4);
    }

    html:not(.dark) .nav-link-icon {
        background: rgba(27, 67, 50, 0.07);
        color: rgba(27, 67, 50, 0.65);
        border-color: rgba(27, 67, 50, 0.12);
    }
    html:not(.dark) .nav-link:hover .nav-link-icon {
        background: rgba(27, 67, 50, 0.12);
        color: var(--g-deep);
    }
    html:not(.dark) .nav-link.active .nav-link-icon {
        background: linear-gradient(135deg, #1B4332, #52B788);
        color: #A8E800;
        border-color: rgba(27,67,50,0.3);
        box-shadow: 0 3px 12px rgba(27, 67, 50, 0.3);
    }

    /* ── Nav text ── */
    .nav-link-text { flex: 1; }
    .nav-link-label {
        font-size: 0.82rem; font-weight: 600; display: block;
        color: rgba(240, 255, 244, 0.72);
        transition: color 0.15s;
    }
    .nav-link.active .nav-link-label { color: #C5F500; }
    .nav-link:hover .nav-link-label { color: #f0fff4; }

    html:not(.dark) .nav-link-label { color: rgba(17, 24, 17, 0.72); }
    html:not(.dark) .nav-link.active .nav-link-label { color: var(--g-deep); }
    html:not(.dark) .nav-link:hover .nav-link-label { color: var(--ink); }

    .nav-link-sub {
        font-size: 0.68rem; color: rgba(240, 255, 244, 0.28); display: block; margin-top: 0.5px;
        transition: color 0.15s;
    }
    .nav-link.active .nav-link-sub { color: rgba(168, 232, 0, 0.55); }
    .nav-link:hover .nav-link-sub { color: rgba(240, 255, 244, 0.48); }

    html:not(.dark) .nav-link-sub { color: rgba(17, 24, 17, 0.35); }
    html:not(.dark) .nav-link.active .nav-link-sub { color: rgba(27, 67, 50, 0.58); }
    html:not(.dark) .nav-link:hover .nav-link-sub { color: rgba(17, 24, 17, 0.52); }

    /* ── Badge chip ── */
    .nav-badge {
        font-size: 0.62rem; font-weight: 700; padding: 2px 7px; border-radius: 99px;
        background: linear-gradient(135deg, #1B4332, #2D6A4F);
        color: #A8E800;
        box-shadow: 0 2px 8px rgba(27, 67, 50, 0.35);
        flex-shrink: 0;
    }
    .nav-badge.green {
        background: linear-gradient(135deg, #A8E800, #C5F500);
        color: #111811;
        box-shadow: 0 2px 8px rgba(168, 232, 0, 0.3);
    }
    .nav-badge.orange {
        background: linear-gradient(135deg, #ea580c, #f97316);
        color: #fff;
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.3);
    }
    .nav-badge.blue {
        background: linear-gradient(135deg, #1d4ed8, #3b82f6);
        color: #fff;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    }

    /* ── Ripple effect ── */
    .nav-link::after {
        content: '';
        position: absolute; inset: 0; border-radius: inherit;
        background: radial-gradient(circle, rgba(168,232,0,0.15) 0%, transparent 70%);
        opacity: 0; transform: scale(0);
        transition: opacity 0.4s, transform 0.4s;
        pointer-events: none;
    }
    .nav-link:active::after {
        opacity: 1; transform: scale(1);
        transition: opacity 0s, transform 0s;
    }

    /* ── Divider ── */
    .sidebar-divider {
        margin: 6px 10px;
        border: none; border-top: 1px solid rgba(168,232,0,0.07);
        transition: border-color 0.3s;
    }
    html:not(.dark) .sidebar-divider { border-top-color: rgba(27,67,50,0.09); }

    /* ── Sidebar footer ── */
    .sidebar-footer {
        padding: 12px 16px;
        border-top: 1px solid rgba(168, 232, 0, 0.1);
        flex-shrink: 0;
        background: linear-gradient(0deg, rgba(27, 67, 50, 0.4) 0%, transparent 100%);
        transition: background 0.3s, border-color 0.3s;
    }

    html:not(.dark) .sidebar-footer {
        background: linear-gradient(0deg, rgba(245, 248, 240, 0.6) 0%, transparent 100%);
        border-top-color: rgba(27, 67, 50, 0.1);
    }

    .sidebar-footer-inner {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 12px; border-radius: 10px;
        background: rgba(168, 232, 0, 0.05);
        border: 1px solid rgba(168, 232, 0, 0.1);
        transition: background 0.3s, border-color 0.3s;
    }

    html:not(.dark) .sidebar-footer-inner {
        background: rgba(27, 67, 50, 0.05);
        border-color: rgba(27, 67, 50, 0.12);
    }

    .footer-logo-dot {
        width: 28px; height: 28px; border-radius: 8px;
        background: linear-gradient(135deg, #1B4332, #2D6A4F);
        display: flex; align-items: center; justify-content: center;
        color: #A8E800; font-size: 0.7rem;
        box-shadow: 0 2px 10px rgba(27,67,50,0.4);
        flex-shrink: 0;
    }
    .footer-text { flex: 1; }
    .footer-app-name {
        font-size: 0.75rem; font-weight: 700;
        color: rgba(240, 255, 244, 0.8); display: block; line-height: 1.2;
        transition: color 0.3s;
    }
    .footer-version {
        font-size: 0.63rem; color: rgba(168, 232, 0, 0.45); letter-spacing: 0.04em;
        transition: color 0.3s;
    }

    html:not(.dark) .footer-app-name { color: rgba(17, 24, 17, 0.8); }
    html:not(.dark) .footer-version { color: rgba(27, 67, 50, 0.55); }

    .footer-status {
        width: 7px; height: 7px; border-radius: 50%;
        background: #A8E800;
        box-shadow: 0 0 6px rgba(168, 232, 0, 0.5);
        animation: status-pulse 2s ease-in-out infinite;
    }
    @keyframes status-pulse {
        0%, 100% { box-shadow: 0 0 4px rgba(168,232,0,0.4); }
        50%       { box-shadow: 0 0 12px rgba(168,232,0,0.7); }
    }

    /* Light mode body bg */
    html:not(.dark) body {
        background-color: #F5F8F0 !important;
    }
</style>

<div x-data class="h-full">

    <!-- Mobile backdrop -->
    <div x-show="$store.sidebar.isOpen"
         x-cloak
         class="sidebar-backdrop lg:hidden"
         @click="$store.sidebar.isOpen = false">
    </div>

    <!-- Sidebar -->
    <aside x-show="$store.sidebar.isOpen"
           x-cloak
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0 -translate-x-full"
           x-transition:enter-end="opacity-100 translate-x-0"
           x-transition:leave="transition ease-in duration-250"
           x-transition:leave-start="opacity-100 translate-x-0"
           x-transition:leave-end="opacity-0 -translate-x-full"
           class="sidebar-shell">

        <!-- Header -->
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="sidebar-brand-dot"></div>
                <span class="sidebar-brand-label">Navigation</span>
            </div>
        </div>

        <!-- Nav links -->
        <nav class="sidebar-nav"
             @click="if (($event.target.tagName === 'A' || $event.target.closest('a')) && window.innerWidth < 1024) {
                 $store.sidebar.isOpen = false
             }">
            @if($role === 'admin')
                @include('layouts.sidebar-admin')
            @elseif($role === 'vet')
                @include('layouts.sidebar-vet')
            @elseif($role === 'receptionist')
                @include('layouts.sidebar-receptionist')
            @elseif($role === 'assistant')
                @include('layouts.sidebar-receptionist')
            @else
                @include('layouts.sidebar-admin')
            @endif
        </nav>

        <!-- Footer -->
        <div class="sidebar-footer">
            <div class="sidebar-footer-inner">
                <div class="footer-logo-dot">
                    <i class="fas fa-paw"></i>
                </div>
                <div class="footer-text">
                    <span class="footer-app-name">{{ config('app.name') }}</span>
                    <span class="footer-version">v1.0 · Stable</span>
                </div>
                <div class="footer-status" title="System online"></div>
            </div>
        </div>

    </aside>
</div>