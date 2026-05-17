<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\MedicalFormula;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class MedicalFormulaController extends Controller
{
    public function store(Request $request, Pet $pet): RedirectResponse
    {
        $this->ensureDoctorOrAdmin();

        $validated = $request->validate([
            'formula_date' => 'required|date',
            'instructions' => 'nullable|string|max:2000',
            'medications' => 'required|array|min:1',
            'medications.*.name' => 'required|string|max:255',
            'medications.*.dose' => 'required|string|max:255',
            'medications.*.frequency' => 'required|string|max:255',
            'medications.*.duration' => 'required|string|max:255',
        ], [
            'medications.required' => 'Debe agregar al menos un medicamento a la fórmula.',
            'medications.min' => 'Debe agregar al menos un medicamento a la fórmula.',
            'medications.*.name.required' => 'El nombre del medicamento es obligatorio.',
            'medications.*.dose.required' => 'La dosis es obligatoria.',
            'medications.*.frequency.required' => 'La frecuencia es obligatoria.',
            'medications.*.duration.required' => 'La duración es obligatoria.',
        ]);

        $validated['pet_id'] = $pet->id;
        $validated['doctor_id'] = Auth::id();

        MedicalFormula::create($validated);

        return redirect()->route('medical_records.show', $pet)
            ->with('success', 'Fórmula médica guardada exitosamente.');
    }

    public function destroy(MedicalFormula $medicalFormula): RedirectResponse
    {
        $this->ensureDoctorOrAdmin();

        $petId = $medicalFormula->pet_id;
        $medicalFormula->delete();

        return redirect()->route('medical_records.show', $petId)
            ->with('success', 'Fórmula médica eliminada exitosamente.');
    }

    private function ensureDoctorOrAdmin(): void
    {
        $role = (string) (Auth::user()->role ?? '');

        if (! in_array($role, ['admin', 'doctor'], true)) {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }
    }
}
