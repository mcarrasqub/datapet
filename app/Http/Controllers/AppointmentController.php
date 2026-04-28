<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax() || $request->has('start') || $request->has('end')) {
            $appointments = Appointment::with(['doctor', 'pet'])->get();
            $events = $appointments->map(function ($appointment) {
                $statusColor = $appointment->getStatus() === 'canceled' ? '#dc3545' : '#76a75d';

                return [
                    'id' => $appointment->getId(),
                    'title' => 'Cita: '.$appointment->pet->getName(),
                    'start' => $appointment->getDate().'T'.$appointment->getStartTime(),
                    'end' => $appointment->getDate().'T'.$appointment->getEndTime(),
                    'backgroundColor' => $statusColor,
                    'borderColor' => $statusColor,
                    'extendedProps' => [
                        'doctor_id' => $appointment->getDoctorId(),
                        'pet_id' => $appointment->getPetId(),
                        'doctor_name' => $appointment->doctor->getName().' '.$appointment->doctor->getLastname(),
                        'pet_name' => $appointment->pet->getName(),
                        'status' => $appointment->getStatus(),
                        'reason' => $appointment->getReason(),
                    ],
                ];
            });

            return response()->json($events);
        }

        $viewData = [
            'doctors' => User::where('role', 'doctor')->get(),
            'pets' => Pet::all(),
        ];

        return view('appointments.index', compact('viewData'));
    }

    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        Appointment::create($request->validated());

        return redirect()->route('appointments.index')->with('success', 'Cita creada exitosamente.');
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $appointment->update($request->validated());

        return redirect()->route('appointments.index')->with('success', 'Cita actualizada exitosamente.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Cita eliminada exitosamente.');
    }
}
