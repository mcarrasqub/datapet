<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentDoctorController extends Controller
{
    public function index(): View
    {
        return view('appointments.doctor_index');
    }

    public function events(Request $request): JsonResponse
    {
        $appointments = Appointment::with(['pet'])
            ->where('doctor_id', auth()->id())
            ->get();

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
                    'pet_name' => $appointment->pet->getName(),
                    'status' => $appointment->getStatus(),
                    'reason' => $appointment->getReason(),
                ],
            ];
        });

        return response()->json($events);
    }
}
