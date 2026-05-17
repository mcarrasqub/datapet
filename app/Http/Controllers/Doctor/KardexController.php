<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\KardexEntry;
use App\Models\Pet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KardexController extends Controller
{
    public function store(Request $request, Pet $pet): RedirectResponse
    {
        $role = (string) (Auth::user()->role ?? '');
        if (! in_array($role, ['admin', 'doctor'], true)) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $validated = $request->validate([
            'entry_date' => 'required|date',
            'animal_type' => 'required|string|in:huron,loro,conejo,erizo,iguana',
            'parameters' => 'required|array',
        ]);

        KardexEntry::create([
            'pet_id' => $pet->id,
            'doctor_id' => Auth::id(),
            'entry_date' => $validated['entry_date'],
            'animal_type' => $validated['animal_type'],
            'parameters' => $validated['parameters'],
        ]);

        return redirect()->route('medical_records.show', $pet)
            ->with('success', 'Registro de Kardex creado con éxito.');
    }

    public function destroy(KardexEntry $kardexEntry): RedirectResponse
    {
        $role = (string) (Auth::user()->role ?? '');
        if (! in_array($role, ['admin', 'doctor'], true)) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $petId = $kardexEntry->pet_id;
        $kardexEntry->delete();

        return redirect()->route('medical_records.show', $petId)
            ->with('success', 'Registro de Kardex eliminado con éxito.');
    }
}
