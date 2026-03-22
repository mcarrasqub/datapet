<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $roles = [
            'admin' => 'Administrador',
            'doctor' => 'Doctor Veterinario',
            'client' => 'Cliente',
        ];

        $query = User::query();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && array_key_exists($request->input('role'), $roles)) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'doctor' => User::where('role', 'doctor')->count(),
            'client' => User::where('role', 'client')->count(),
        ];

        return view('admin.users.index', compact('users', 'roles', 'counts'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,doctor'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // Split full name into name and lastname if possible.
        $fullName = trim($data['name']);
        $parts = preg_split('/\s+/', $fullName);
        $firstName = $parts[0] ?? '';
        $lastName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

        $user = User::create([
            'name' => $firstName,
            'lastname' => $lastName,
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => true,
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Usuario \"{$user->name}\" creado correctamente.");
    }

    /**
     * Toggle the user's active status.
     */
    public function toggleStatus(User $user)
    {
        $user->status = ! (bool) $user->status;
        $user->save();

        $statusLabel = $user->status ? 'activado' : 'desactivado';
        return redirect()->route('users.index')
            ->with('success', "Usuario \"{$user->name}\" $statusLabel exitosamente.");
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Usuario \"{$user->name}\" eliminado correctamente.");
    }
}
