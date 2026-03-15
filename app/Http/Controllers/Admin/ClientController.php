<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role' => 'client',
            'status' => true,
        ]);

        if (auth()->user()->role === 'doctor') {
            return redirect()->route('clients.create')->with('success', 'Cliente creado exitosamente.');
        }

        return redirect()->route('users.index')->with('success', 'Cliente creado exitosamente.');
    }
}