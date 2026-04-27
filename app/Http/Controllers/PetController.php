<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetRequest;
use App\Models\MedicalExam;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PetController extends Controller
{
    public function index(): View
    {
        $viewData = [];
        $viewData['pets'] = Pet::with('owner')->get();

        return view('pets.index')->with('viewData', $viewData);
    }

    public function create(): View
    {
        $viewData = [];
        $viewData['clients'] = User::all();

        return view('pets.create')->with('viewData', $viewData);
    }

    public function store(PetRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('pets', 'public');
        }

        Pet::create($data);

        return redirect()->route('home.index')->with('success', 'Mascota registrada exitosamente');
    }

    public function show(Pet $pet): View
    {
        $this->ensureCanAccessPet($pet);

        $viewData = [];
        $viewData['pet'] = $pet;

        $layout = Auth::check() && Auth::user()->role !== 'client' ? 'layouts.dashboard' : 'layouts.app';

        return view('pets.show')->with('viewData', $viewData)->with('layout', $layout);
    }

    public function exams(): View
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'client') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $petIds = $user->pets()->pluck('id');
        $selectedPetId = request()->integer('pet_id');

        $viewData = [];
        $viewData['pets'] = $user->pets()->get();
        $examsQuery = MedicalExam::with(['pet'])
            ->whereIn('pet_id', $petIds)
            ->orderByDesc('uploaded_at');

        if ($selectedPetId && $petIds->contains($selectedPetId)) {
            $examsQuery->where('pet_id', $selectedPetId);
        }

        $viewData['selectedPetId'] = $selectedPetId;
        $viewData['medicalExams'] = $examsQuery->get();

        return view('pets.exams')->with('viewData', $viewData);
    }

    public function edit(Pet $pet): View
    {
        $this->ensureCanAccessPet($pet);

        $viewData = [];
        $viewData['pet'] = $pet;
        $viewData['clients'] = User::all();

        return view('pets.edit')->with('viewData', $viewData);
    }

    public function update(PetRequest $request, Pet $pet): RedirectResponse
    {
        $this->ensureCanAccessPet($pet);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($pet->getPhoto()) {
                Storage::disk('public')->delete($pet->getPhoto());
            }
            $data['photo'] = $request->file('photo')->store('pets', 'public');
        }

        $pet->update($data);

        return redirect()->route('home.index')->with('success', 'Mascota actualizada');
    }

    public function destroy(Pet $pet): RedirectResponse
    {
        $this->ensureCanAccessPet($pet);

        if ($pet->getPhoto()) {
            Storage::disk('public')->delete($pet->getPhoto());
        }

        $pet->delete();

        return redirect()->route('home.index')->with('success', 'Mascota eliminada');
    }

    private function ensureCanAccessPet(Pet $pet): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        if (in_array($user->role, ['admin', 'doctor'], true)) {
            return;
        }

        if ($user->role === 'client' && (int) $pet->user_id === (int) $user->id) {
            return;
        }

        abort(403, 'No tienes permisos para acceder a esta mascota.');
    }
}
