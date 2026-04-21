<?php

use App\Models\DoctorTask;
use Illuminate\Support\Facades\Artisan;

// Crear tareas de prueba
$doctor_id = 4;

try {
    DoctorTask::create([
        'doctor_id' => $doctor_id,
        'title' => 'Revisar historia clínica - COMPLETADA',
        'description' => 'Tarea de prueba completada',
        'status' => 'completed',
        'due_date' => '2026-04-15',
        'priority' => 'high',
    ]);

    DoctorTask::create([
        'doctor_id' => $doctor_id,
        'title' => 'Revisar examen de laboratorio - COMPLETADA',
        'description' => 'Otra tarea completada',
        'status' => 'completed',
        'due_date' => '2026-04-18',
        'priority' => 'medium',
    ]);

    DoctorTask::create([
        'doctor_id' => $doctor_id,
        'title' => 'Revisar radiografía - VENCIDA',
        'description' => 'Tarea con fecha pasada (2026-04-10)',
        'status' => 'pending',
        'due_date' => '2026-04-10',
        'priority' => 'high',
    ]);

    echo "✅ Tareas de prueba creadas exitosamente\n";
    echo "\nTareas totales: " . DoctorTask::count() . "\n";

    echo "\nPor estado:\n";
    $byStatus = DoctorTask::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->get();

    foreach ($byStatus as $row) {
        echo "  - {$row->status}: {$row->count}\n";
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
