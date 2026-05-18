<?php

namespace App\Http\Controllers;

use App\Models\VaccinationReminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class VaccinationReminderController extends Controller
{
    public function destroy(VaccinationReminder $reminder): RedirectResponse
    {
        $role = (string) (Auth::user()->role ?? '');

        if (! in_array($role, ['admin', 'doctor'], true)) {
            abort(403, 'No tienes permisos para eliminar recordatorios.');
        }

        $reminder->update(['status' => 'completed']);

        return back()->with('success', 'Recordatorio marcado como completado y eliminado de la vista.');
    }

    public function dismiss(VaccinationReminder $reminder): RedirectResponse
    {
        if ((int) $reminder->user_id !== (int) Auth::id()) {
            abort(403, 'No tienes permisos para descartar este recordatorio.');
        }

        $reminder->update(['status' => 'dismissed']);

        return back()->with('success', 'Recordatorio descartado de la vista.');
    }
}
