<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Http\Requests\StoreMedicalRecordRequest;
use App\Http\Requests\UpdateMedicalRecordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MedicalRecordController extends Controller
{
    public function index(): View
    {
        $pets = Pet::with('medicalRecords.doctor')->get();
        
        $selectedPet = $pets->first();
        $medicalRecords = $selectedPet ? $selectedPet->medicalRecords()->orderByDesc('visited_at')->get() : collect();
        $lastVisit = $selectedPet ? $selectedPet->medicalRecords()->orderByDesc('visited_at')->first() : null;

        $viewData = [];
        $viewData['pets'] = $pets;
        $viewData['selectedPet'] = $selectedPet;
        $viewData['medicalRecords'] = $medicalRecords;
        $viewData['lastVisit'] = $lastVisit;

        return view('medical_records.medical_records', $viewData);
    }

    public function show(Pet $pet): View
    {
        $pets = Pet::with('medicalRecords.doctor')->get();
        $medicalRecords = $pet->medicalRecords()->orderByDesc('visited_at')->get();
        $lastVisit = $pet->medicalRecords()->orderByDesc('visited_at')->first();

        $viewData = [];
        $viewData['pets'] = $pets;
        $viewData['selectedPet'] = $pet;
        $viewData['medicalRecords'] = $medicalRecords;
        $viewData['lastVisit'] = $lastVisit;

        return view('medical_records.medical_records', $viewData);
    }

    public function create(Pet $pet): View
    {
        $viewData = [];
        $viewData['pet'] = $pet;

        return view('medical_records.create', $viewData);
    }

    public function store(StoreMedicalRecordRequest $request, Pet $pet): RedirectResponse
    {
        $validated = $request->validated();

        // Procesar fotos
        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                if ($photo) {
                    $path = $photo->store('medical_records', 'public');
                    $photos[] = $path;
                }
            }
        }

        $validated['pet_id'] = $pet->id;
        $validated['doctor_id'] = Auth::id();
        $validated['photos'] = $photos;

        MedicalRecord::create($validated);

        return redirect()->route('medical_records.show', $pet)
                       ->with('success', 'Registro médico creado con éxito');
    }

    public function edit(MedicalRecord $medicalRecord): View
    {
        $viewData = [];
        $viewData['record'] = $medicalRecord;
        $viewData['pet'] = $medicalRecord->pet;

        return view('medical_records.edit', $viewData);
    }

    public function update(UpdateMedicalRecordRequest $request, MedicalRecord $medicalRecord): RedirectResponse
    {
        $validated = $request->validated();

        // Procesar fotos
        $photos = $medicalRecord->photos ?? [];
        
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                if ($photo && count($photos) < 3) {
                    $path = $photo->store('medical_records', 'public');
                    $photos[] = $path;
                }
            }
        }

        $validated['photos'] = $photos;
        $medicalRecord->update($validated);

        return redirect()->route('medical_records.show', $medicalRecord->pet)
                       ->with('success', 'Registro médico actualizado con éxito');
    }

    public function destroy(MedicalRecord $medicalRecord): RedirectResponse
    {
        // Eliminar fotos
        if ($medicalRecord->photos) {
            foreach ($medicalRecord->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $petId = $medicalRecord->pet_id;
        $medicalRecord->delete();

        return redirect()->route('medical_records.show', $petId)
                       ->with('success', 'Registro médico eliminado con éxito');
    }
}