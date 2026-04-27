<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDoctorTaskStatusRequest;
use App\Models\DoctorTask;
use App\Models\MedicalExam;
use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorTaskController extends Controller
{
    private const CONSULTATION_PREFIX = 'La consulta del ';

    private const TASK_KEY_RECORD_SEGMENT = ':record:';

    public function index(Request $request): View
    {
        $this->ensureAdmin();

        $this->syncAutomaticTasks();

        $doctorFilter = $request->input('doctor_id');
        $statusFilter = $request->input('status');

        // Construir consulta base para tareas con filtros de doctor
        $tasksBaseQuery = DoctorTask::query()
            ->when($doctorFilter, function ($query) use ($doctorFilter) {
                $query->where('doctor_id', $doctorFilter);
            });

        // Obtener todas las tareas para calcular métricas globales (sin filtro de estado)
        $allTasksForMetrics = (clone $tasksBaseQuery)->get();

        // Calcular métricas globales por estado
        $globalMetrics = [
            'total' => $allTasksForMetrics->count(),
            'pending' => $allTasksForMetrics->filter(function ($task) {
                return $task->status === 'pending' && ! ($task->is_overdue || $task->status === 'overdue');
            })->count(),
            'completed' => $allTasksForMetrics->where('status', 'completed')->count(),
            'overdue' => $allTasksForMetrics->filter(function ($task) {
                return $task->is_overdue || $task->status === 'overdue';
            })->count(),
        ];

        // Cargar doctores con sus tareas filtradas correctamente
        $doctors = User::query()
            ->where('role', 'doctor')
            ->when($doctorFilter, function ($query) use ($doctorFilter) {
                $query->where('id', $doctorFilter);
            })
            ->with([
                'doctorTasks' => function ($query) use ($statusFilter) {
                    // Si se selecciona un filtro de estado, aplicarlo
                    if ($statusFilter) {
                        if ($statusFilter === 'overdue') {
                            // Para "vencida": tareas con status='overdue' O tareas donde la fecha límite ya pasó y no están completadas
                            $query->where(function ($q) {
                                $q->where('status', 'overdue')
                                    ->orWhere(function ($subQ) {
                                        $subQ->where('status', '!=', 'completed')
                                            ->where('status', '!=', 'overdue')
                                            ->whereNotNull('due_date')
                                            ->whereRaw('due_date < CURDATE()');
                                    });
                            });
                        } else {
                            // Para otros estados
                            if ($statusFilter === 'pending') {
                                // Para "pendiente": solo tareas pending que NO estén vencidas
                                $query->where('status', 'pending')
                                    ->where(function ($q) {
                                        $q->whereNull('due_date')
                                            ->orWhereRaw('due_date >= CURDATE()');
                                    });
                            } else {
                                // Para "completed", filtrar exactamente por ese estado
                                $query->where('status', $statusFilter);
                            }
                        }
                    }

                    $priorityOrder = DB::connection()->getDriverName() === 'sqlite'
                        ? "CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END"
                        : "FIELD(priority, 'high', 'medium', 'low')";

                    $query->orderByRaw($priorityOrder)
                        ->orderBy('due_date')
                        ->orderByDesc('created_at');
                },
            ])
            ->orderBy('name')
            ->get();

        // Las métricas a mostrar son las globales (respetan el filtro de doctor pero no el de estado)
        $metrics = $globalMetrics;

        return view('dashboard.admin_tasks', [
            'doctors' => $doctors,
            'allDoctors' => User::where('role', 'doctor')->orderBy('name')->get(),
            'doctorFilter' => $doctorFilter,
            'statusFilter' => $statusFilter,
            'metrics' => $metrics,
        ]);
    }

    public function updateStatus(UpdateDoctorTaskStatusRequest $request, DoctorTask $task): RedirectResponse
    {
        $this->ensureAdmin();

        $task->update([
            'status' => $request->validated('status'),
        ]);

        return back()->with('success', 'Estado de la tarea actualizado correctamente.');
    }

    public function updateOwnStatus(Request $request, DoctorTask $task): RedirectResponse
    {
        // Verificar que el doctor autenticado es dueño de esta tarea
        if (! Auth::check() || Auth::user()->id !== $task->doctor_id || Auth::user()->role !== 'doctor') {
            abort(403, 'No tienes permiso para actualizar esta tarea.');
        }

        $request->validate([
            'status' => 'required|in:pending,completed',
        ]);

        $task->update([
            'status' => $request->input('status'),
        ]);

        return back()->with('success', 'Tarea actualizada correctamente.');
    }

    public function destroy(DoctorTask $task): RedirectResponse
    {
        $this->ensureAdmin();

        $task->delete();

        return back()->with('success', 'Tarea eliminada correctamente.');
    }

    private function ensureAdmin(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
    }

    private function syncAutomaticTasks(): void
    {
        $doctors = User::where('role', 'doctor')->get();
        $allDoctorIds = $doctors->pluck('id')->all();

        foreach ($doctors as $doctor) {
            $activeKeys = [];

            $examTasks = $this->buildUnreviewedExamTasks($allDoctorIds);
            foreach ($examTasks as $taskData) {
                $activeKeys[] = $taskData['task_key'];
                $this->upsertSystemTask($taskData);
            }

            $recordTasks = $this->buildIncompleteRecordTasks((int) $doctor->id);
            foreach ($recordTasks as $taskData) {
                $activeKeys[] = $taskData['task_key'];
                $this->upsertSystemTask($taskData);
            }

            $cleanupQuery = DoctorTask::where('doctor_id', $doctor->id)
                ->where('is_system', true)
                ->where('status', '!=', 'completed'); // Proteger tareas completadas

            if (! empty($activeKeys)) {
                $cleanupQuery->whereNotIn('task_key', $activeKeys);
            }

            $cleanupQuery->delete();
        }
    }

    /**
     * @param  array<string, mixed>  $taskData
     */
    private function upsertSystemTask(array $taskData): void
    {
        $existingTask = DoctorTask::where('task_key', $taskData['task_key'])->first();

        // Si la tarea ya existe y está completada, mantenerla completada
        if ($existingTask && $existingTask->status === 'completed') {
            $taskData['status'] = 'completed';
        }

        DoctorTask::updateOrCreate(
            ['task_key' => $taskData['task_key']],
            $taskData
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildUnreviewedExamTasks(array $allDoctorIds): array
    {
        $exams = MedicalExam::query()
            ->with(['pet.medicalRecords', 'uploader', 'medicalRecord'])
            ->whereNull('reviewed_by_doctor_at')
            ->whereHas('uploader', function (Builder $query) {
                $query->where('role', 'client');
            })
            ->get();

        $tasks = [];
        foreach ($exams as $exam) {
            $petName = $exam->pet?->name ?? 'Mascota';
            $targetDoctorIds = $this->resolveTargetDoctorIdsForExam($exam, $allDoctorIds);

            foreach ($targetDoctorIds as $targetDoctorId) {
                $tasks[] = [
                    'doctor_id' => $targetDoctorId,
                    'title' => 'Revisar examen externo: '.($exam->title ?: $exam->original_name),
                    'description' => 'El cliente subió un examen para '.$petName.' y aún no ha sido revisado por el doctor.',
                    'status' => 'pending',
                    'due_date' => optional($exam->uploaded_at)->toDateString(),
                    'priority' => 'high',
                    'is_system' => true,
                    'source_type' => 'medical_exam',
                    'source_id' => $exam->id,
                    'task_key' => 'doctor:'.$targetDoctorId.':exam:'.$exam->id.':review',
                ];
            }
        }

        return $tasks;
    }

    /**
     * @return array<int>
     */
    private function resolveTargetDoctorIdsForExam(MedicalExam $exam, array $allDoctorIds): array
    {
        $doctorIds = collect();

        if ($exam->medicalRecord && $exam->medicalRecord->doctor_id) {
            $doctorIds->push((int) $exam->medicalRecord->doctor_id);
        }

        if ($exam->pet) {
            $doctorIds = $doctorIds->merge(
                $exam->pet->medicalRecords->pluck('doctor_id')->all()
            );
        }

        $doctorIds = $doctorIds
            ->filter()
            ->unique()
            ->values()
            ->all();

        return ! empty($doctorIds) ? $doctorIds : $allDoctorIds;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildIncompleteRecordTasks(int $doctorId): array
    {
        $records = MedicalRecord::query()
            ->where('doctor_id', $doctorId)
            ->where(function (Builder $query) {
                $query->whereNull('diagnosis')
                    ->orWhere('diagnosis', '')
                    ->orWhereNull('treatment')
                    ->orWhere('treatment', '')
                    ->orWhereNull('notes')
                    ->orWhere('notes', '');
            })
            ->get();

        $tasks = [];
        foreach ($records as $record) {
            $visitDate = optional($record->visited_at)->format('Y-m-d') ?? 'sin fecha';

            if (empty((string) $record->diagnosis)) {
                $tasks[] = [
                    'doctor_id' => $doctorId,
                    'title' => 'Completar historial clínico (diagnóstico)',
                    'description' => self::CONSULTATION_PREFIX.$visitDate.' no tiene diagnóstico registrado.',
                    'status' => 'pending',
                    'due_date' => optional($record->visited_at)->toDateString(),
                    'priority' => 'high',
                    'is_system' => true,
                    'source_type' => 'medical_record',
                    'source_id' => $record->id,
                    'task_key' => 'doctor:'.$doctorId.self::TASK_KEY_RECORD_SEGMENT.$record->id.':diagnosis',
                ];
            }

            if (empty((string) $record->treatment)) {
                $tasks[] = [
                    'doctor_id' => $doctorId,
                    'title' => 'Completar receta/tratamiento',
                    'description' => self::CONSULTATION_PREFIX.$visitDate.' no tiene tratamiento o receta registrada.',
                    'status' => 'pending',
                    'due_date' => optional($record->visited_at)->toDateString(),
                    'priority' => 'medium',
                    'is_system' => true,
                    'source_type' => 'medical_record',
                    'source_id' => $record->id,
                    'task_key' => 'doctor:'.$doctorId.self::TASK_KEY_RECORD_SEGMENT.$record->id.':treatment',
                ];
            }

            if (empty((string) $record->notes)) {
                $tasks[] = [
                    'doctor_id' => $doctorId,
                    'title' => 'Completar kardex/seguimiento',
                    'description' => self::CONSULTATION_PREFIX.$visitDate.' no tiene notas de seguimiento (kardex).',
                    'status' => 'pending',
                    'due_date' => optional($record->visited_at)->toDateString(),
                    'priority' => 'low',
                    'is_system' => true,
                    'source_type' => 'medical_record',
                    'source_id' => $record->id,
                    'task_key' => 'doctor:'.$doctorId.self::TASK_KEY_RECORD_SEGMENT.$record->id.':notes',
                ];
            }
        }

        return $tasks;
    }
}
