<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;      



class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $roles = [
            'admin' => 'Administrador',
            'doctor' => 'Doctor Veterinario',
            'client' => 'Cliente',
        ];

        $query = User::query();
        $searchInput = $request->input('search', '');
        $roleInput = $request->input('role', '');

        // Filtro de búsqueda
        if (!empty($searchInput)) {
            $search = trim($searchInput);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por rol
        if (!empty($roleInput) && array_key_exists($roleInput, $roles)) {
            $query->where('role', $roleInput);
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

        return view('admin.users.index', compact('users', 'roles', 'counts', 'searchInput', 'roleInput'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UserRequest $request): RedirectResponse
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
            'name' => $data['name'],
            'lastname' => $data['lastname'],
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
    public function toggleStatus(User $user): RedirectResponse
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
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Usuario \"{$user->name}\" eliminado correctamente.");
    }
}
