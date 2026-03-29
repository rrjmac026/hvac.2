<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, fn ($q) =>
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
            )
            ->when($request->role, fn ($q) => $q->where('role', $request->role))
            ->when($request->status, fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->orderBy('last_name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'confirmed', Password::defaults()],
            'phone'      => ['nullable', 'string', 'max:20'],
            'role'       => ['required', Rule::in([
                                User::ROLE_ADMIN,
                                User::ROLE_VET,
                                User::ROLE_ASSISTANT,
                                User::ROLE_RECEPTIONIST,
                            ])],
            'is_active'  => ['boolean'],
            'photo_path' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('photo_path')) {
            $validated['photo_path'] = $request->file('photo_path')->store('staff-photos', 'public');
        }

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Staff account created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['appointments', 'medicalRecords', 'prescriptions']);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'   => ['nullable', 'confirmed', Password::defaults()],
            'phone'      => ['nullable', 'string', 'max:20'],
            'role'       => ['required', Rule::in([
                                User::ROLE_ADMIN,
                                User::ROLE_VET,
                                User::ROLE_ASSISTANT,
                                User::ROLE_RECEPTIONIST,
                            ])],
            'is_active'  => ['boolean'],
            'photo_path' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('photo_path')) {
            $validated['photo_path'] = $request->file('photo_path')->store('staff-photos', 'public');
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Staff account updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete(); // soft delete

        return redirect()->route('admin.users.index')
            ->with('success', 'Staff account removed.');
    }
}
