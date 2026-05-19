<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        // Disparador Automático: Escanear y generar recordatorios de vacunas próximas a vencer
        resolve(\App\Services\VaccinationReminderService::class)->sendUpcomingReminders();

        $viewData = [];
        $viewData['user'] = Auth::user();
        $viewData['pets'] = $viewData['user']->pets;
        $viewData['reminders'] = \App\Models\VaccinationReminder::where('user_id', Auth::id())
            ->whereIn('status', ['sent', 'completed'])
            ->get();

        $layout = Auth::check() && Auth::user()->role !== 'client' ? 'layouts.dashboard' : 'layouts.app';

        return view('home.index')->with('viewData', $viewData)->with('layout', $layout);
    }

    public function notifications(): View
    {
        resolve(\App\Services\VaccinationReminderService::class)->sendUpcomingReminders();

        $viewData = [];
        $viewData['user'] = Auth::user();
        $viewData['reminders'] = \App\Models\VaccinationReminder::where('user_id', Auth::id())
            ->whereIn('status', ['sent', 'completed'])
            ->orderByDesc('sent_at')
            ->get();

        $layout = Auth::check() && Auth::user()->role !== 'client' ? 'layouts.dashboard' : 'layouts.app';

        return view('notifications.index')->with('viewData', $viewData)->with('layout', $layout);
    }

    public function appointments(): View
    {
        $viewData = [];
        $viewData['user'] = Auth::user();

        $viewData['appointments'] = \App\Models\Appointment::with(['pet', 'doctor'])
            ->whereHas('pet', function ($query) use ($viewData) {
                $query->where('user_id', $viewData['user']->id);
            })
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $layout = Auth::check() && Auth::user()->role !== 'client' ? 'layouts.dashboard' : 'layouts.app';

        return view('appointments.client_index')->with('viewData', $viewData)->with('layout', $layout);
    }
}
