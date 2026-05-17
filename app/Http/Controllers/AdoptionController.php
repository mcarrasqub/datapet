<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdoptionRequestRequest;
use App\Http\Requests\PetStoreRequest;
use App\Models\AdoptionRequest;
use App\Models\Pet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdoptionController extends Controller
{
    public function index(): View
    {
        $viewData = [];
        $viewData['pets'] = Pet::where('available_for_adoption', true)->get();

        return view('adoption.index')->with('viewData', $viewData);
    }

    public function show(Pet $pet): View
    {
        if (! $pet->getAvailableForAdoption()) {
            abort(404, 'Esta mascota no está disponible para adopción');
        }

        $viewData = [];
        $viewData['pet'] = $pet;

        return view('adoption.show')->with('viewData', $viewData);
    }

    public function store(AdoptionRequestRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        AdoptionRequest::create($data);

        return redirect()->route('adoption.index')
            ->with('success', 'Solicitud enviada exitosamente');
    }

    public function adminIndex(): View
    {
        $this->ensureAdmin();

        $viewData = [];
        $viewData['requests'] = AdoptionRequest::with(['pet', 'user'])
            ->orderByDesc('created_at')
            ->get();

        $viewData['pets'] = Pet::where('available_for_adoption', true)
            ->orWhereNotNull('adoption_description')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.adoption.index')->with('viewData', $viewData);
    }

    public function edit(Pet $pet): View
    {
        $this->ensureAdmin();

        $viewData = [];
        $viewData['pet'] = $pet;

        return view('admin.adoption.edit')->with('viewData', $viewData);
    }

    public function updatePet(Pet $pet, \App\Http\Requests\PetStoreRequest $request): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validated();

        // Checkbox
        $data['available_for_adoption'] = $request->has('available_for_adoption');

        // Manejo de imagen
        if ($request->hasFile('photo')) {
            // Eliminar foto anterior si existe
            if ($pet->getPhoto() && \Illuminate\Support\Facades\Storage::disk('public')->exists($pet->getPhoto())) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pet->getPhoto());
            }
            $data['photo'] = $request->file('photo')->store('pets', 'public');
        }

        $pet->update($data);

        return redirect()->route('adoption.admin.index')
            ->with('success', 'Mascota de adopción actualizada correctamente');
    }

    public function approve(AdoptionRequest $adoptionRequest): RedirectResponse
    {
        $this->ensureAdmin();

        $adoptionRequest->setStatus('approved');
        $adoptionRequest->save();

        return redirect()->back()->with('success', 'Solicitud aprobada');
    }

    public function reject(AdoptionRequest $adoptionRequest): RedirectResponse
    {
        $this->ensureAdmin();

        $adoptionRequest->setStatus('rejected');
        $adoptionRequest->save();

        return redirect()->back()->with('success', 'Solicitud rechazada');
    }

    public function create(): View
    {
        $this->ensureAdmin();

        return view('admin.adoption.create');
    }

    public function storePet(PetStoreRequest $request): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validated();

        // Asignar usuario creador
        $data['user_id'] = Auth::id();

        // Manejo de imagen
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('pets', 'public');
        }

        // Checkbox
        $data['available_for_adoption'] = $request->has('available_for_adoption');

        Pet::create($data);

        return redirect()->route('adoption.admin.index')
            ->with('success', 'Mascota creada correctamente');
    }

    private function ensureAdmin(): void
    {
        $role = (string) (Auth::user()->role ?? '');

        if ($role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
    }
}
