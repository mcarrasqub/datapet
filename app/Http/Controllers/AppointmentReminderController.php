<?php
// app/Http/Controllers/AppointmentReminderController.php

namespace App\Http\Controllers;

use App\Models\AppointmentReminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AppointmentReminderController extends Controller
{
    public function destroy(AppointmentReminder $reminder): RedirectResponse
    {
        $role = (string) (Auth::user()->role ?? '');

        if (! in_array($role, ['admin', 'doctor'], true)) {
            abort(403, 'No tienes permisos para eliminar recordatorios.');
        }

        $reminder->update(['status' => 'completed']);

        return back()->with('success', 'Recordatorio marcado como completado.');
    }

    public function dismiss(AppointmentReminder $reminder): RedirectResponse
    {
        if ((int) $reminder->user_id !== (int) Auth::id()) {
            abort(403, 'No tienes permisos para descartar este recordatorio.');
        }

        $reminder->update(['status' => 'dismissed']);

        return back()->with('success', 'Recordatorio descartado.');
    }
}