<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a paginated listing of system users with search and role filtering.
     */
    public function index(Request $request): View
    {
        $query = User::with('roles')->latest();

        // Search by name or email
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $roleFilter = $request->input('role');
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('name', $roleFilter);
            });
        }

        $users = $query->paginate(15)->withQueryString();

        // Summary counts for header stat cards
        $totalUsers = User::count();
        $adminCount = User::role('admin')->count();
        $editorCount = User::role('editor')->count();
        $authorCount = User::role('author')->count();
        $roles = Role::all();

        return view('admin.users.index', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'adminCount' => $adminCount,
            'editorCount' => $editorCount,
            'authorCount' => $authorCount,
            'roles' => $roles,
            'currentSearch' => $request->input('search', ''),
            'currentRole' => $request->input('role', ''),
        ]);
    }

    /**
     * Show the form for creating a new system user.
     */
    public function create(): View
    {
        $roles = Role::all();

        return view('admin.users.create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created user with designated role and permissions.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'is_admin' => $validated['role'] === 'admin',
            'role' => $validated['role'],
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')
            ->with('status', "User '{$user->name}' created successfully with role '{$validated['role']}'.");
    }

    /**
     * Show the form for editing an existing system user.
     */
    public function edit(User $user): View
    {
        $user->load('roles');
        $roles = Role::all();
        $userRole = $user->roles->first()?->name ?? $user->role ?? 'user';

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $roles,
            'userRole' => $userRole,
        ]);
    }

    /**
     * Update an existing user's information, role, and optional password.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Guard against removing the last admin or stripping own admin access
        if (auth()->id() === $user->id && $user->hasRole('admin') && $validated['role'] !== 'admin') {
            return back()->withInput()->withErrors([
                'role' => 'You cannot revoke administrator privileges from your own active session.',
            ]);
        }

        $userData = [
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'is_admin' => $validated['role'] === 'admin',
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('status', "User '{$user->name}' updated successfully.");
    }

    /**
     * Remove the specified user from the system.
     */
    public function destroy(User $user): RedirectResponse
    {
        // 1. Prevent self-deletion
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Action prohibited: You cannot delete your own user account.');
        }

        // 2. Prevent deleting the last remaining administrator
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return back()->with('error', 'Action prohibited: You cannot delete the sole remaining administrator.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', "User '{$userName}' was deleted successfully.");
    }
}
