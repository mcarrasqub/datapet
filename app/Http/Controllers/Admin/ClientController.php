<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;


class ClientController extends Controller
{
    public function create(): View
    {
        return view('admin.clients.create');
    }

    public function store(ClientRequest $request): RedirectResponse
    {
        $data = $request->validated();

        User::create([
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'client',
            'status' => true,
        ]);

        if (auth()->user()->role === 'doctor') {
            return redirect()->route('clients.create')->with('success', 'Cliente creado exitosamente.');
        }

        return redirect()->route('users.index')->with('success', 'Cliente creado exitosamente.');
    }
}