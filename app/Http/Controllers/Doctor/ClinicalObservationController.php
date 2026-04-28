<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\ClinicalObservation;
use App\Models\MedicalRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClinicalObservationController extends Controller
{
    public function store(Request $request, MedicalRecord $medicalRecord): RedirectResponse
    {
        $this->ensureDoctorOrAdmin();

        $validated = $request->validate([
            'observation' => 'required|string|max:5000',
        ]);

        ClinicalObservation::create([
            'medical_record_id' => $medicalRecord->id,
            'doctor_id' => Auth::id(),
            'observation' => $validated['observation'],
        ]);

        return back()->with('success', 'Observación clínica registrada con éxito.');
    }

    public function edit(ClinicalObservation $clinicalObservation): View
    {
        $this->ensureDoctorOrAdmin();

        $clinicalObservation->load('medicalRecord.pet', 'doctor');

        return view('medical_records.observation_edit', [
            'observation' => $clinicalObservation,
            'medicalRecord' => $clinicalObservation->medicalRecord,
            'pet' => $clinicalObservation->medicalRecord->pet,
        ]);
    }

    public function update(Request $request, ClinicalObservation $clinicalObservation): RedirectResponse
    {
        $this->ensureDoctorOrAdmin();

        $validated = $request->validate([
            'observation' => 'required|string|max:5000',
        ]);

        $clinicalObservation->update([
            'observation' => $validated['observation'],
        ]);

        return redirect()->route('medical_records.show', $clinicalObservation->medicalRecord->pet)
            ->with('success', 'Observación clínica actualizada con éxito.');
    }

    public function destroy(ClinicalObservation $clinicalObservation): RedirectResponse
    {
        $this->ensureDoctorOrAdmin();

        $pet = $clinicalObservation->medicalRecord->pet;
        $clinicalObservation->delete();

        return redirect()->route('medical_records.show', $pet)
            ->with('success', 'Observación clínica eliminada con éxito.');
    }

    private function ensureDoctorOrAdmin(): void
    {
        $role = (string) (Auth::user()->role ?? '');

        if (! in_array($role, ['admin', 'doctor'], true)) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
    }
}
