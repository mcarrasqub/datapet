<?php

// Script de diagnóstico para verificar tareas completadas
require 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DoctorTask;
use App\Models\User;

echo "=== DIAGNÓSTICO DE TAREAS ===\n\n";

// 1. Total de tareas
$totalTasks = DoctorTask::count();
echo "Total de tareas en BD: $totalTasks\n\n";

// 2. Tareas por estado
echo "Tareas por estado:\n";
$byStatus = DoctorTask::selectRaw('status, COUNT(*) as count')
    ->groupBy('status')
    ->get();

foreach ($byStatus as $row) {
    echo "  - {$row->status}: {$row->count}\n";
}

echo "\n";

// 3. Tareas completadas
$completed = DoctorTask::where('status', 'completed')->count();
echo "Tareas completadas: $completed\n";

// 4. Tareas vencidas
$overdue = DoctorTask::where('status', 'overdue')->count();
echo "Tareas con status='overdue': $overdue\n";

// 5. Vencidas calculadas (fecha < hoy)
$overdueCalculated = DoctorTask::where('status', '!=', 'completed')
    ->where('status', '!=', 'overdue')
    ->whereNotNull('due_date')
    ->whereRaw('due_date < CURDATE()')
    ->count();
echo "Tareas vencidas (fecha < hoy): $overdueCalculated\n\n";

// 6. Por doctor
echo "Tareas por doctor:\n";
$byDoctor = DoctorTask::selectRaw('doctor_id, status, COUNT(*) as count')
    ->groupBy('doctor_id', 'status')
    ->orderBy('doctor_id')
    ->with('doctor')
    ->get();

$doctors = User::where('role', 'doctor')->get()->keyBy('id');

$currentDoctor = null;
foreach ($byDoctor as $row) {
    if ($currentDoctor !== $row->doctor_id) {
        $currentDoctor = $row->doctor_id;
        $doctorName = $doctors[$row->doctor_id]->name ?? 'Unknown';
        echo "\n  Doctor ID $row->doctor_id ($doctorName):\n";
    }
    echo "    - {$row->status}: {$row->count}\n";
}

echo "\n";

// 7. Verificar si hay problemas
if ($completed === 0 && $totalTasks > 0) {
    echo "⚠️ ALERTA: No hay tareas completadas pero sí hay tareas totales\n";
}

if ($totalTasks === 0) {
    echo "⚠️ ALERTA: No hay tareas en la base de datos\n";
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";
