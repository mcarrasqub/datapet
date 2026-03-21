<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $pets = Pet::with('medicalRecords.doctor')->get();
        
        $selectedPet = $pets->first();
        $medicalRecords = $selectedPet ? $selectedPet->medicalRecords()->orderByDesc('visited_at')->get() : collect();
        $lastVisit = $selectedPet ? $selectedPet->medicalRecords()->orderByDesc('visited_at')->first() : null;

        return view('medical_records.medical_records', [
            'pets' => $pets,
            'selectedPet' => $selectedPet,
            'medicalRecords' => $medicalRecords,
            'lastVisit' => $lastVisit,
        ]);
    }

    public function show(Pet $pet)
    {
        $pets = Pet::with('medicalRecords.doctor')->get();
        $medicalRecords = $pet->medicalRecords()->orderByDesc('visited_at')->get();
        $lastVisit = $pet->medicalRecords()->orderByDesc('visited_at')->first();

        return view('medical_records.medical_records', [
            'pets' => $pets,
            'selectedPet' => $pet,
            'medicalRecords' => $medicalRecords,
            'lastVisit' => $lastVisit,
        ]);
    }

    public function create(Pet $pet)
    {
        return view('medical_records.create', [
            'pet' => $pet,
        ]);
    }

    public function store(Request $request, Pet $pet)
    {
        $validated = $request->validate([
            'visited_at' => 'required|date',
            'reason' => 'required|string|max:255',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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

    public function edit(MedicalRecord $medicalRecord)
    {
        return view('medical_records.edit', [
            'record' => $medicalRecord,
            'pet' => $medicalRecord->pet,
        ]);
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'visited_at' => 'required|date',
            'reason' => 'required|string|max:255',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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

    public function destroy(MedicalRecord $medicalRecord)
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