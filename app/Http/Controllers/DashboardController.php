<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pet;
use App\Models\MedicalRecord;
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

            return view('dashboard.admin', compact(
                'totalUsers', 'totalAdmins', 'totalDoctors', 'totalClients'
            ));
        } elseif ($user->role === 'doctor') {
            $totalPatients = Pet::count();
            $consultasMes = MedicalRecord::where('doctor_id', $user->id)
                ->whereMonth('visited_at', now()->month)
                ->count();

            return view('dashboard.doctor', compact('totalPatients', 'consultasMes'));
        }

        return redirect()->route('home.index');
    }
}
