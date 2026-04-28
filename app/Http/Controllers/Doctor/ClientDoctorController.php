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
}
