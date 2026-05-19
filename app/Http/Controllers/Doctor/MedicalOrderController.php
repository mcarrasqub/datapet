<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\MedicalOrder;
use App\Models\Pet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalOrderController extends Controller
{
    public function store(Request $request, Pet $pet): RedirectResponse
    {
        $role = (string) (Auth::user()->role ?? '');
        if (! in_array($role, ['admin', 'doctor'], true)) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $validated = $request->validate([
            'order_date' => 'required|date',
            'order_type' => 'required|string|in:Laboratorio,Imagenología,Cirugía / Procedimiento,Otros',
            'description' => 'required|string|max:5000',
        ]);

        $medicalOrder = MedicalOrder::create([
            'pet_id' => $pet->id,
            'doctor_id' => Auth::id(),
            'order_date' => $validated['order_date'],
            'order_type' => $validated['order_type'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        // Crear la notificación para el propietario de la mascota
        $owner = $pet->owner;
        if ($owner) {
            $doctorName = Auth::user()->name;
            $petName = $pet->getName();
            $orderType = $validated['order_type'];

            $message = sprintf(
                'El Dr. %s ha emitido una nueva orden clínica de tipo %s para tu mascota %s.',
                $doctorName,
                $orderType,
                $petName
            );

            \App\Models\VaccinationReminder::create([
                'vaccination_id' => null,
                'medical_order_id' => $medicalOrder->id,
                'pet_id' => $pet->id,
                'user_id' => $owner->id,
                'phone' => $owner->phone ?? '3000000000',
                'message' => $message,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        return redirect()->route('medical_records.show', $pet)
            ->with('success', 'Orden clínica emitida con éxito.');
    }

    public function updateStatus(Request $request, MedicalOrder $medicalOrder): RedirectResponse
    {
        $role = (string) (Auth::user()->role ?? '');
        if (! in_array($role, ['admin', 'doctor'], true)) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:pending,completed,cancelled',
        ]);

        $medicalOrder->update([
            'status' => $validated['status'],
        ]);

        return redirect()->route('medical_records.show', $medicalOrder->pet_id)
            ->with('success', 'Estado de la orden clínica actualizado con éxito.');
    }

    public function destroy(MedicalOrder $medicalOrder): RedirectResponse
    {
        $role = (string) (Auth::user()->role ?? '');
        if (! in_array($role, ['admin', 'doctor'], true)) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $petId = $medicalOrder->pet_id;
        $medicalOrder->delete();

        return redirect()->route('medical_records.show', $petId)
            ->with('success', 'Orden clínica eliminada con éxito.');
    }
}
