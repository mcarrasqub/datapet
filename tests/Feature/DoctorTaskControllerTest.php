<?php

namespace Tests\Feature;

use App\Models\DoctorTask;
use App\Models\MedicalExam;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorTaskControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $doctor;
    private Pet $pet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->doctor = User::factory()->create(['role' => 'doctor']);
        
        $client = User::factory()->create(['role' => 'client']);
        $this->pet = Pet::factory()->create(['user_id' => $client->id]);
    }

    /**
     * Test: El index genera tareas automáticas (Cubre la lógica de sincronización)
     */
    public function test_index_syncs_system_tasks(): void
    {
        $this->actingAs($this->admin);

        // 1. Crear un examen médico sin revisar (debe generar tarea)
        MedicalExam::create([
            'pet_id' => $this->pet->id,
            'uploaded_by' => $this->doctor->id,
            'title' => 'Examen Pendiente',
            'file_path' => 'path/test.pdf',
            'original_name' => 'test.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 100,
            'uploaded_at' => now(),
        ]);

        // 2. Crear una consulta incompleta (diagnosis vacío)
        MedicalRecord::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'visited_at' => now(),
            'reason' => 'Consulta',
            'diagnosis' => '', // Incompleto
            'treatment' => '',
            'notes' => '',
        ]);

        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        // Verificar que se crearon las tareas de sistema en la BD
        $this->assertDatabaseHas('doctor_tasks', ['is_system' => true]);
    }

    /**
     * Test: Actualizar estado de una tarea (Cubre el método updateStatus)
     */
    public function test_admin_can_update_task_status(): void
    {
        $this->actingAs($this->admin);

        $task = DoctorTask::create([
            'doctor_id' => $this->doctor->id,
            'title' => 'Tarea Test',
            'status' => 'pending',
            'priority' => 'medium',
            'is_system' => false
        ]);

        $response = $this->patch(route('tasks.updateStatus', $task), [
            'status' => 'completed'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('doctor_tasks', [
            'id' => $task->id,
            'status' => 'completed'
        ]);
    }

    /**
     * Test: El doctor puede actualizar su propia tarea (Cubre updateOwnStatus)
     */
    public function test_doctor_can_complete_own_task(): void
    {
        $this->actingAs($this->doctor);

        $task = DoctorTask::create([
            'doctor_id' => $this->doctor->id,
            'title' => 'Mi Tarea',
            'status' => 'pending',
            'priority' => 'low',
            'is_system' => false
        ]);

        $response = $this->patch(route('tasks.updateOwnStatus', $task), [
            'status' => 'completed'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('doctor_tasks', ['id' => $task->id, 'status' => 'completed']);
    }

    /**
     * Test Seguridad: Un doctor no puede editar tareas de otro doctor
     */
    public function test_doctor_cannot_update_others_tasks(): void
    {
        $otherDoctor = User::factory()->create(['role' => 'doctor']);
        $task = DoctorTask::create([
            'doctor_id' => $otherDoctor->id,
            'title' => 'Tarea Ajena',
            'status' => 'pending',
            'priority' => 'high',
            'is_system' => false
        ]);

        $this->actingAs($this->doctor); // Logueado como doctor A tratando de editar a doctor B

        $response = $this->patch(route('tasks.updateOwnStatus', $task), [
            'status' => 'completed'
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test: Eliminar tarea (Cubre destroy)
     */
    public function test_admin_can_delete_task(): void
    {
        $this->actingAs($this->admin);

        $task = DoctorTask::create([
            'doctor_id' => $this->doctor->id,
            'title' => 'Borrar',
            'status' => 'pending',
            'priority' => 'low',
            'is_system' => false
        ]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect();
        $this->assertDatabaseMissing('doctor_tasks', ['id' => $task->id]);
    }
}