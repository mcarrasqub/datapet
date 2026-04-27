<?php

namespace Tests\Feature;

use App\Models\DoctorTask;
use App\Models\User;
use Tests\TestCase;

class DoctorTasksTest extends TestCase
{
    private User $admin;
    private User $doctorAaron;
    private User $doctorBeatriz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin',
            'lastname' => 'General',
            'role' => 'admin',
        ]);

        $this->doctorAaron = User::factory()->create([
            'name' => 'Aaron',
            'lastname' => 'Lopez',
            'role' => 'doctor',
        ]);

        $this->doctorBeatriz = User::factory()->create([
            'name' => 'Beatriz',
            'lastname' => 'Perez',
            'role' => 'doctor',
        ]);
    }

    /**
     * Happy path: el admin visualiza las tareas agrupadas por doctor y
     * las tareas vencidas se resaltan en el dashboard.
     *
     * Criteria covered:
     * - Tasks are grouped by doctor
     * - Overdue tasks are highlighted
     */
    public function test_admin_can_view_tasks_grouped_by_doctor_and_overdue_highlighted(): void
    {
        DoctorTask::create([
            'doctor_id' => $this->doctorAaron->id,
            'title' => 'Revisar examen pendiente',
            'description' => 'Examen subido por cliente',
            'status' => 'pending',
            'due_date' => now()->subDay()->toDateString(),
            'priority' => 'high',
            'is_system' => false,
            'source_type' => 'medical_exam',
            'source_id' => 1,
            'task_key' => 'doctor:'.$this->doctorAaron->id.':exam:1:review',
        ]);

        DoctorTask::create([
            'doctor_id' => $this->doctorAaron->id,
            'title' => 'Completar kardex',
            'description' => 'Observación de seguimiento',
            'status' => 'pending',
            'due_date' => now()->addDay()->toDateString(),
            'priority' => 'medium',
            'is_system' => false,
            'source_type' => 'medical_record',
            'source_id' => 2,
            'task_key' => 'doctor:'.$this->doctorAaron->id.':record:2:notes',
        ]);

        DoctorTask::create([
            'doctor_id' => $this->doctorBeatriz->id,
            'title' => 'Completar diagnóstico',
            'description' => 'Consulta sin diagnóstico final',
            'status' => 'pending',
            'due_date' => now()->addDay()->toDateString(),
            'priority' => 'high',
            'is_system' => false,
            'source_type' => 'medical_record',
            'source_id' => 3,
            'task_key' => 'doctor:'.$this->doctorBeatriz->id.':record:3:diagnosis',
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('Dr(a). Aaron Lopez', $content);
        $this->assertStringContainsString('Dr(a). Beatriz Perez', $content);
        $this->assertStringContainsString('Revisar examen pendiente', $content);
        $this->assertStringContainsString('Completar kardex', $content);
        $this->assertStringContainsString('Completar diagnóstico', $content);
        $this->assertMatchesRegularExpression('/Vencidas:\s*1/', $content);
        $this->assertStringContainsString('table-danger', $content);
        $this->assertMatchesRegularExpression('/Dr\(a\)\. Aaron Lopez.*Revisar examen pendiente/s', $content);
        $this->assertMatchesRegularExpression('/Dr\(a\)\. Beatriz Perez.*Completar diagnóstico/s', $content);
    }

    /**
     * Happy path: el admin actualiza el estado de una tarea.
     *
     * Criteria covered:
     * - Status can be updated
     */
    public function test_admin_can_update_task_status(): void
    {
        $task = DoctorTask::create([
            'doctor_id' => $this->doctorAaron->id,
            'title' => 'Revisar examen externo',
            'description' => 'Examen pendiente de revisión',
            'status' => 'pending',
            'due_date' => now()->addDay()->toDateString(),
            'priority' => 'high',
            'is_system' => false,
            'source_type' => 'medical_exam',
            'source_id' => 10,
            'task_key' => 'doctor:'.$this->doctorAaron->id.':exam:10:review',
        ]);

        $this->actingAs($this->admin);

        $response = $this->patch(route('tasks.updateStatus', $task), [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Estado de la tarea actualizado correctamente.');

        $this->assertDatabaseHas('doctor_tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
    }

    /**
     * Caso negativo: un usuario que no es admin no debe acceder al dashboard de tareas.
     */
    public function test_non_admin_cannot_access_tasks_dashboard(): void
    {
        $doctor = User::factory()->create([
            'name' => 'Doctor',
            'lastname' => 'SinPermiso',
            'role' => 'doctor',
        ]);

        $this->actingAs($doctor);

        $response = $this->get(route('tasks.index'));

        $response->assertForbidden();
    }
}
