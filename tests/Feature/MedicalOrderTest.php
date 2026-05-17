<?php

namespace Tests\Feature;

use App\Models\DoctorTask;
use App\Models\MedicalExam;
use App\Models\MedicalOrder;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MedicalOrderTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $doctor;

    private User $client;

    private Pet $pet;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->doctor = User::factory()->create(['role' => 'doctor']);
        $this->client = User::factory()->create(['role' => 'client']);

        $this->pet = Pet::factory()->create([
            'user_id' => $this->client->id,
            'species' => 'Perro',
        ]);
    }

    /** @test */
    public function doctor_can_create_clinical_order()
    {
        $this->actingAs($this->doctor);

        $response = $this->post(route('orders.store', $this->pet), [
            'order_date' => '2026-05-17',
            'order_type' => 'Laboratorio',
            'description' => 'Realizar perfil hepático',
        ]);

        $response->assertRedirect(route('medical_records.show', $this->pet));
        $response->assertSessionHas('success', 'Orden clínica emitida con éxito.');

        $this->assertDatabaseHas('medical_orders', [
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'order_type' => 'Laboratorio',
            'description' => 'Realizar perfil hepático',
            'status' => 'pending',
        ]);

        $order = MedicalOrder::first();
        $this->assertEquals('2026-05-17', $order->order_date->format('Y-m-d'));
    }

    /** @test */
    public function order_creation_validates_inputs()
    {
        $this->actingAs($this->doctor);

        $response = $this->post(route('orders.store', $this->pet), [
            'order_date' => 'not-a-date',
            'order_type' => 'Invalido',
            'description' => '',
        ]);

        $response->assertSessionHasErrors(['order_date', 'order_type', 'description']);
    }

    /** @test */
    public function client_cannot_create_clinical_order()
    {
        $this->actingAs($this->client);

        $response = $this->post(route('orders.store', $this->pet), [
            'order_date' => '2026-05-17',
            'order_type' => 'Laboratorio',
            'description' => 'Perfil hepático',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseEmpty('medical_orders');
    }

    /** @test */
    public function doctor_can_update_order_status()
    {
        $order = MedicalOrder::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'order_date' => '2026-05-17',
            'order_type' => 'Imagenología',
            'description' => 'Radiografía de tórax',
            'status' => 'pending',
        ]);

        $this->actingAs($this->doctor);

        $response = $this->patch(route('orders.updateStatus', $order), [
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('medical_records.show', $this->pet));
        $response->assertSessionHas('success', 'Estado de la orden clínica actualizado con éxito.');

        $this->assertEquals('completed', $order->fresh()->status);
    }

    /** @test */
    public function doctor_can_delete_clinical_order()
    {
        $order = MedicalOrder::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'order_date' => '2026-05-17',
            'order_type' => 'Otros',
            'description' => 'Control de peso',
            'status' => 'pending',
        ]);

        $this->actingAs($this->doctor);

        $response = $this->delete(route('orders.destroy', $order));

        $response->assertRedirect(route('medical_records.show', $this->pet));
        $response->assertSessionHas('success', 'Orden clínica eliminada con éxito.');

        $this->assertDatabaseEmpty('medical_orders');
    }

    /** @test */
    public function client_uploading_exam_can_link_to_order_and_automatic_review_completes_it()
    {
        // 1. Doctor creates clinical order
        $order = MedicalOrder::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'order_date' => '2026-05-17',
            'order_type' => 'Laboratorio',
            'description' => 'Hemograma',
            'status' => 'pending',
        ]);

        // 2. Client uploads an exam linked to this order
        $this->actingAs($this->client);
        $file = UploadedFile::fake()->create('hemograma.pdf', 1000, 'application/pdf');

        $response = $this->post(route('medical_exams.store', $this->pet), [
            'title' => 'Resultado de Hemograma',
            'category' => 'Laboratorio',
            'exam_date' => '2026-05-17',
            'files' => [$file],
            'medical_order_id' => $order->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('medical_exams', [
            'pet_id' => $this->pet->id,
            'title' => 'Resultado de Hemograma',
            'medical_order_id' => $order->id,
        ]);

        $exam = MedicalExam::first();
        $this->assertEquals($order->id, $exam->medical_order_id);

        // 3. Admin syncs doctor tasks. "Revisar examen" task gets created for the doctor
        $this->actingAs($this->admin);
        $this->get(route('tasks.index'))->assertOk();

        $taskKey = 'doctor:'.$this->doctor->id.':exam:'.$exam->id.':review';
        $this->assertDatabaseHas('doctor_tasks', [
            'doctor_id' => $this->doctor->id,
            'task_key' => $taskKey,
            'status' => 'pending',
            'source_type' => 'medical_exam',
            'source_id' => $exam->id,
        ]);

        // 4. Doctor views/reviews the exam, completing the review task and the clinical order automatically
        $this->actingAs($this->doctor);

        // Ver el examen ya no completa la revisión automáticamente
        $viewResponse = $this->get(route('medical_exams.view', $exam));
        $viewResponse->assertOk();
        $this->assertEquals('pending', $order->fresh()->status);

        // Confirmar la revisión explícitamente
        $this->post(route('medical_exams.complete_review', $exam))->assertRedirect();

        // The order should now be completed!
        $this->assertEquals('completed', $order->fresh()->status);

        // The review task should now be completed!
        $task = DoctorTask::where('task_key', $taskKey)->first();
        $this->assertEquals('completed', $task->status);
    }

    /** @test */
    public function completing_review_task_from_dashboard_completes_exam_and_order()
    {
        // 1. Doctor creates clinical order
        $order = MedicalOrder::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'order_date' => '2026-05-17',
            'order_type' => 'Laboratorio',
            'description' => 'Hemograma',
            'status' => 'pending',
        ]);

        // 2. Client uploads linked exam
        $exam = MedicalExam::create([
            'pet_id' => $this->pet->id,
            'uploaded_by' => $this->client->id,
            'title' => 'Hemograma',
            'original_name' => 'result.pdf',
            'file_path' => 'medical_exams/pet_1/fake.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_at' => now(),
            'medical_order_id' => $order->id,
        ]);

        // 3. Admin task sync
        $this->actingAs($this->admin);
        $this->get(route('tasks.index'))->assertOk();

        $task = DoctorTask::where('source_type', 'medical_exam')->where('source_id', $exam->id)->first();
        $this->assertNotNull($task);
        $this->assertEquals('pending', $task->status);

        // 4. Admin marks task as completed
        $response = $this->patch(route('tasks.updateStatus', $task), [
            'status' => 'completed',
        ]);
        $response->assertRedirect();

        // Underlyings should now be completed!
        $this->assertNotNull($exam->fresh()->reviewed_by_doctor_at);
        $this->assertEquals('completed', $order->fresh()->status);
    }
}
