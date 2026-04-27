<?php

namespace App\Http\Controllers;

use App\Models\MedicalExam;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $totalUsers = User::whereIn('role', ['admin', 'doctor'])->count();
            $totalAdmins = User::where('role', 'admin')->count();
            $totalDoctors = User::where('role', 'doctor')->count();
            $totalClients = User::where('role', 'client')->count();

            $consultasSemana = MedicalRecord::whereBetween('visited_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count();

            $consultasHoy = MedicalRecord::whereDate('visited_at', now()->toDateString())->count();

            $recordsMesActual = MedicalRecord::whereBetween('visited_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])->count();
            $recordsMesAnterior = MedicalRecord::whereBetween('visited_at', [
                now()->subMonthNoOverflow()->startOfMonth(),
                now()->subMonthNoOverflow()->endOfMonth(),
            ])->count();

            $growthPercentage = 0;
            if ($recordsMesAnterior > 0) {
                $growthPercentage = (int) round((($recordsMesActual - $recordsMesAnterior) / $recordsMesAnterior) * 100);
            } elseif ($recordsMesActual > 0) {
                $growthPercentage = 100;
            }

            $recentUserActivities = User::latest()->take(5)->get()->map(function ($entry) {
                return [
                    'icon' => $entry->role === 'doctor' ? 'bi-person-plus' : 'bi-person-add',
                    'title' => $entry->role === 'doctor' ? 'Nuevo doctor registrado' : 'Nuevo usuario registrado',
                    'description' => trim($entry->name.' '.($entry->lastname ?? '')),
                    'time' => $entry->created_at,
                ];
            });

            $recentPetActivities = Pet::with('owner')->latest()->take(5)->get()->map(function ($entry) {
                return [
                    'icon' => 'bi-heart',
                    'title' => 'Nueva mascota registrada',
                    'description' => trim($entry->name.' - '.($entry->owner?->name ?? 'Sin propietario')),
                    'time' => $entry->created_at,
                ];
            });

            $recentMedicalActivities = MedicalRecord::with('pet')->latest('visited_at')->take(5)->get()->map(function ($entry) {
                return [
                    'icon' => 'bi-file-earmark-medical',
                    'title' => 'Nueva consulta médica',
                    'description' => trim(($entry->pet?->name ?? 'Mascota').' - '.$entry->reason),
                    'time' => $entry->created_at,
                ];
            });

            $recentActivities = (new Collection)
                ->merge($recentUserActivities)
                ->merge($recentPetActivities)
                ->merge($recentMedicalActivities)
                ->sortByDesc('time')
                ->take(6)
                ->values();

            return view('dashboard.admin', compact(
                'totalUsers',
                'totalAdmins',
                'totalDoctors',
                'totalClients',
                'consultasSemana',
                'consultasHoy',
                'growthPercentage',
                'recentActivities'
            ));
        } elseif ($user->role === 'doctor') {
            $today = now();

            $totalPatients = Pet::count();
            $consultasHoy = MedicalRecord::where('doctor_id', $user->id)
                ->whereDate('visited_at', $today->toDateString())
                ->count();
            $consultasMes = MedicalRecord::where('doctor_id', $user->id)
                ->whereMonth('visited_at', $today->month)
                ->whereYear('visited_at', $today->year)
                ->count();

            $pendingExamsQuery = MedicalExam::query()
                ->with(['pet.owner'])
                ->whereNull('reviewed_by_doctor_at')
                ->whereHas('uploader', function ($query) {
                    $query->where('role', 'client');
                })
                ->orderByDesc('uploaded_at');

            $pendingExams = $pendingExamsQuery->take(4)->get();
            $examsPendientes = (clone $pendingExamsQuery)->count();

            $agendaHoy = \App\Models\Appointment::with(['pet.owner'])
                ->where('doctor_id', $user->id)
                ->where('date', $today->toDateString())
                ->where('status', 'scheduled')
                ->orderBy('start_time')
                ->get();

            return view('dashboard.doctor', compact(
                'totalPatients',
                'consultasHoy',
                'consultasMes',
                'examsPendientes',
                'pendingExams',
                'agendaHoy'
            ));
        }

        return redirect()->route('home.index');
    }
}
