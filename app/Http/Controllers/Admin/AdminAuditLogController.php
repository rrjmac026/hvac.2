<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->user_id, fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->action, fn ($q) => $q->where('action', $request->action))
            ->when($request->model, fn ($q) => $q->where('auditable_type', 'like', "%{$request->model}%"))
            ->when($request->from, fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        $staff = User::orderBy('last_name')->get(['id', 'first_name', 'last_name']);

        $actions = ['created', 'updated', 'deleted', 'viewed', 'login', 'logout'];

        return view('admin.audit-logs.index', compact('logs', 'staff', 'actions'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');

        return view('admin.audit-logs.show', compact('auditLog'));
    }
}
