<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    /**
     * Mostrar listado de mascotas y su historial
     */
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

    /**
     * Mostrar historial de una mascota específica
     */
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

    /**
     * Mostrar formulario para crear nuevo registro
     */
    public function create(Pet $pet)
    {
        return view('medical_records.create', [
            'pet' => $pet,
        ]);
    }

    /**
     * Guardar nuevo registro médico
     */
    public function store(Request $request, Pet $pet)
    {
        $validated = $request->validate([
            'visited_at' => 'required|date',
            'reason' => 'required|string|max:255',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $validated['pet_id'] = $pet->id;
        $validated['doctor_id'] = Auth::user()->id;

        MedicalRecord::create($validated);

        return redirect()->route('medical_records.show', $pet)
                       ->with('success', 'Registro médico creado con éxito');
    }

    /**
     * Mostrar formulario para editar registro
     */
    public function edit(MedicalRecord $medicalRecord)
    {
        return view('medical_records.edit', [
            'record' => $medicalRecord,
            'pet' => $medicalRecord->pet,
        ]);
    }

    /**
     * Actualizar registro médico
     */
    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'visited_at' => 'required|date',
            'reason' => 'required|string|max:255',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $medicalRecord->update($validated);

        return redirect()->route('medical_records.show', $medicalRecord->pet)
                       ->with('success', 'Registro médico actualizado con éxito');
    }

    /**
     * Eliminar registro médico
     */
    public function destroy(MedicalRecord $medicalRecord)
    {
        $petId = $medicalRecord->pet_id;
        $medicalRecord->delete();

        return redirect()->route('medical_records.show', $petId)
                       ->with('success', 'Registro médico eliminado con éxito');
    }
}