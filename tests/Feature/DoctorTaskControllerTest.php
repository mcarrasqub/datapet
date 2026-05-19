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
            'is_system' => false,
        ]);

        $response = $this->patch(route('tasks.updateStatus', $task), [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('doctor_tasks', [
            'id' => $task->id,
            'status' => 'completed',
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
            'is_system' => false,
        ]);

        $response = $this->patch(route('tasks.updateOwnStatus', $task), [
            'status' => 'completed',
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
            'is_system' => false,
        ]);

        $this->actingAs($this->doctor); // Logueado como doctor A tratando de editar a doctor B

        $response = $this->patch(route('tasks.updateOwnStatus', $task), [
            'status' => 'completed',
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
            'is_system' => false,
        ]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect();
        $this->assertDatabaseMissing('doctor_tasks', ['id' => $task->id]);
    }

    /**
     * Test index filtering by doctor_id.
     */
    public function test_admin_can_filter_tasks_by_doctor(): void
    {
        $this->actingAs($this->admin);

        $otherDoctor = User::factory()->create(['role' => 'doctor', 'name' => 'Luis']);

        $task1 = DoctorTask::create([
            'doctor_id' => $this->doctor->id,
            'title' => 'Tarea Doctor Uno',
            'status' => 'pending',
            'priority' => 'low',
            'is_system' => false,
        ]);

        $task2 = DoctorTask::create([
            'doctor_id' => $otherDoctor->id,
            'title' => 'Tarea Doctor Dos',
            'status' => 'pending',
            'priority' => 'low',
            'is_system' => false,
        ]);

        $response = $this->get(route('tasks.index', ['doctor_id' => $this->doctor->id]));
        $response->assertStatus(200);

        $response->assertSee('Tarea Doctor Uno');
        $response->assertDontSee('Tarea Doctor Dos');
    }

    /**
     * Test index filtering by status.
     */
    public function test_admin_can_filter_tasks_by_status(): void
    {
        $this->actingAs($this->admin);

        // Completed task
        $taskCompleted = DoctorTask::create([
            'doctor_id' => $this->doctor->id,
            'title' => 'Tarea Completada',
            'status' => 'completed',
            'priority' => 'low',
            'is_system' => false,
        ]);

        // Pending task (due in future)
        $taskPending = DoctorTask::create([
            'doctor_id' => $this->doctor->id,
            'title' => 'Tarea Pendiente Futura',
            'status' => 'pending',
            'due_date' => now()->addDays(5)->toDateString(),
            'priority' => 'low',
            'is_system' => false,
        ]);

        // Overdue task (status is pending but due in past)
        $taskOverdue = DoctorTask::create([
            'doctor_id' => $this->doctor->id,
            'title' => 'Tarea Vencida',
            'status' => 'pending',
            'due_date' => now()->subDays(5)->toDateString(),
            'priority' => 'low',
            'is_system' => false,
        ]);

        // Filter: completed
        $response = $this->get(route('tasks.index', ['status' => 'completed']));
        $response->assertStatus(200);
        $response->assertSee('Tarea Completada');
        $response->assertDontSee('Tarea Pendiente Futura');
        $response->assertDontSee('Tarea Vencida');

        // Filter: pending
        $response2 = $this->get(route('tasks.index', ['status' => 'pending']));
        $response2->assertStatus(200);
        $response2->assertSee('Tarea Pendiente Futura');
        $response2->assertDontSee('Tarea Completada');
        $response2->assertDontSee('Tarea Vencida');

        // Filter: overdue
        $response3 = $this->get(route('tasks.index', ['status' => 'overdue']));
        $response3->assertStatus(200);
        $response3->assertSee('Tarea Vencida');
        $response3->assertDontSee('Tarea Completada');
        $response3->assertDontSee('Tarea Pendiente Futura');
    }

    /**
     * Test updateStatus completion cascade to medical exams and orders.
     */
    public function test_admin_completing_exam_task_triggers_completion_cascades(): void
    {
        $this->actingAs($this->admin);

        $order = \App\Models\MedicalOrder::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'order_date' => now(),
            'order_type' => 'Laboratorio',
            'description' => 'Test order',
            'status' => 'pending',
        ]);

        $exam = MedicalExam::create([
            'pet_id' => $this->pet->id,
            'uploaded_by' => User::factory()->create(['role' => 'client'])->id,
            'title' => 'Client exam',
            'file_path' => 'path.pdf',
            'original_name' => 'path.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 100,
            'uploaded_at' => now(),
            'medical_order_id' => $order->id,
        ]);

        $task = DoctorTask::create([
            'doctor_id' => $this->doctor->id,
            'title' => 'Review client exam',
            'status' => 'pending',
            'priority' => 'high',
            'is_system' => true,
            'source_type' => 'medical_exam',
            'source_id' => $exam->id,
            'task_key' => 'doctor:'.$this->doctor->id.':exam:'.$exam->id.':review',
        ]);

        $response = $this->patch(route('tasks.updateStatus', $task), [
            'status' => 'completed',
        ]);

        $response->assertRedirect();

        $this->assertEquals('completed', $task->fresh()->status);

        $exam->refresh();
        $this->assertEquals($this->doctor->id, $exam->reviewed_by_doctor_id);
        $this->assertNotNull($exam->reviewed_by_doctor_at);

        $this->assertEquals('completed', $order->fresh()->status);
    }

    /**
     * Test automatic tasks get cleaned up when records are completed.
     */
    public function test_system_task_cleanup_when_records_completed(): void
    {
        $this->actingAs($this->admin);

        $record = MedicalRecord::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'visited_at' => now(),
            'reason' => 'Consulta',
            'diagnosis' => '', // Incomplete
            'treatment' => 'Some treatment',
            'notes' => 'Some notes',
        ]);

        // Access index to trigger sync
        $this->get(route('tasks.index'));

        $taskKey = 'doctor:'.$this->doctor->id.':record:'.$record->id.':diagnosis';
        $this->assertDatabaseHas('doctor_tasks', [
            'task_key' => $taskKey,
            'status' => 'pending',
        ]);

        // Complete the record's diagnosis
        $record->update(['diagnosis' => 'Sano']);

        // Access index to trigger sync again
        $this->get(route('tasks.index'));

        // Verify task was automatically deleted
        $this->assertDatabaseMissing('doctor_tasks', [
            'task_key' => $taskKey,
        ]);
    }
}
