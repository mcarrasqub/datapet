<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientDoctorController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'client')->with('pets');

        if ($request->filled('client_search')) {
            $query->searchByClient((string) $request->input('client_search'));
        }

        if ($request->filled('pet_search')) {
            $query->searchByPet((string) $request->input('pet_search'));
        }

        $clients = $query->get();

        $viewData = [];
        $viewData['clients'] = $clients;
        $viewData['clientSearch'] = $request->input('client_search');
        $viewData['petSearch'] = $request->input('pet_search');

        return view('dashboard.doctor.clients', $viewData);
    }

    /**
     * Update client details by doctor.
     */
    public function update(Request $request, User $client): \Illuminate\Http\RedirectResponse
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['doctor', 'admin'])) {
            abort(403, 'No autorizado');
        }

        if ($client->role !== 'client') {
            abort(400, 'Solo se pueden actualizar datos de clientes');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $client->id,
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'lastname.required' => 'El apellido es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'phone.required' => 'El teléfono es obligatorio',
        ]);

        $client->update($data);

        return redirect()->back()->with('success', 'Datos del cliente actualizados correctamente.');
    }
}
