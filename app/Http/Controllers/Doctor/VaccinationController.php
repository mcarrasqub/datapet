<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\VaccinationRequest;
use App\Models\Pet;
use App\Models\Vaccination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class VaccinationController extends Controller
{
    public function store(VaccinationRequest $request, Pet $pet): RedirectResponse
    {
        $this->ensureDoctorOrAdmin();

        Vaccination::create([
            'pet_id' => $pet->id,
            'doctor_id' => (int) Auth::id(),
            'vaccine_type' => $request->validated('vaccine_type'),
            'vaccinated_at' => $request->validated('vaccinated_at'),
            'next_due_date' => $request->validated('next_due_date'),
            'notes' => $request->validated('notes'),
        ]);

        return redirect()->route('medical_records.show', $pet)
            ->with('success', 'Vacuna registrada con éxito');
    }

    private function ensureDoctorOrAdmin(): void
    {
        $role = (string) (Auth::user()->role ?? '');

        if (! in_array($role, ['admin', 'doctor'], true)) {
            abort(403, 'No tienes permisos para registrar vacunas.');
        }
    }
}
