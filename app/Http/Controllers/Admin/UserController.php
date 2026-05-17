<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

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
        if (! empty($searchInput)) {
            $search = trim($searchInput);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por rol
        if (! empty($roleInput) && array_key_exists($roleInput, $roles)) {
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
        // Al usar UserRequest, los datos YA vienen validados.
        // Solo tomamos los datos validados usando $request->validated()
        $data = $request->validated();

        // Separar nombre completo si es posible
        $fullName = trim($data['name']);
        $parts = preg_split('/\s+/', $fullName);
        $firstName = $parts[0] ?? '';
        $lastName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

        $user = User::create([
            'name' => $firstName ?: $data['name'],
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
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'No tienes permisos para editar usuarios.');
        }

        $roles = [
            'admin' => 'Administrador',
            'doctor' => 'Doctor Veterinario',
            'client' => 'Cliente',
        ];

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'No tienes permisos para actualizar usuarios.');
        }

        // Eliminamos el $request->validate manual que causaba el conflicto.
        // Ahora usamos las reglas dinámicas del UserRequest
        $data = $request->validated();

        $fullName = trim($data['name']);
        $parts = preg_split('/\s+/', $fullName);
        $firstName = $parts[0] ?? '';
        $lastName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

        $user->name = $firstName ?: $data['name'];
        $user->lastname = $lastName;
        $user->email = $data['email'];
        $user->role = $data['role'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', "Usuario \"{$user->name}\" actualizado correctamente.");
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
    
    if (Auth::user()->role !== 'admin') {
        abort(403, 'No tienes permisos para realizar esta acción.');
    }

    $user->delete();

    return redirect()->route('users.index')
        ->with('success', "Usuario \"{$user->name}\" eliminado correctamente.");
    }
}
