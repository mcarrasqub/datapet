<?php

namespace Tests\Feature;

use App\Models\MedicalExam;
use App\Models\MedicalOrder;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MedicalExamsTest extends TestCase
{
    use RefreshDatabase;

    private User $doctor;

    private Pet $pet;

    private MedicalRecord $medicalRecord;

    protected function setUp(): void
    {
        parent::setUp();

        // Usamos el disco 'local' que es el definido en tu controlador
        Storage::fake('local');

        $this->doctor = User::factory()->create(['role' => 'doctor']);
        $client = User::factory()->create(['role' => 'client']);
        $this->pet = Pet::factory()->create(['user_id' => $client->id]);

        $this->medicalRecord = MedicalRecord::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'visited_at' => now(),
            'reason' => 'Control',
            'diagnosis' => 'Estable',
            'treatment' => 'N/A',
            'notes' => 'Mascota sana',
        ]);
    }

    /**
     * Test de carga de archivos (Store)
     */
    public function test_doctor_can_upload_medical_exam(): void
    {
        $this->actingAs($this->doctor);

        $file = UploadedFile::fake()->create('resultado.pdf', 500);

        $data = [
            'medical_record_id' => $this->medicalRecord->id,
            'title' => 'Hemograma',
            'description' => 'Resultados normales',
            'category' => 'Laboratorio',
            'exam_date' => now()->toDateString(),
            'files' => [$file],
        ];

        $response = $this->post(route('medical_exams.store', $this->pet), $data);

        $response->assertRedirect();

        // Verificación de base de datos
        $this->assertDatabaseHas('medical_exams', [
            'title' => 'Hemograma',
            'pet_id' => $this->pet->id,
        ]);

        // Verificación física del archivo
        $exam = MedicalExam::latest()->first();
        $this->assertTrue(
            Storage::disk('local')->exists($exam->file_path),
            'El archivo no existe en: '.$exam->file_path
        );
    }

    /**
     * Test de visualización (View) - Corregido para evitar error de Header
     */
    public function test_doctor_can_view_medical_exam(): void
    {
        $this->actingAs($this->doctor);

        // 1. Crear el archivo físicamente en el disco fake
        $fileName = 'test_view.pdf';
        $relativePath = 'medical_exams/pet_'.$this->pet->id;
        $fullPath = $relativePath.'/'.$fileName;

        Storage::disk('local')->put($fullPath, 'Fake PDF Content');

        // 2. Crear el registro en la BD apuntando a ese archivo
        $exam = MedicalExam::create([
            'pet_id' => $this->pet->id,
            'medical_record_id' => $this->medicalRecord->id,
            'uploaded_by' => $this->doctor->id,
            'title' => 'Radiografia',
            'file_path' => $fullPath,
            'original_name' => $fileName,
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_at' => now(),
        ]);

        $response = $this->get(route('medical_exams.view', $exam));

        $response->assertOk();

        // Usamos una expresión regular o aserción parcial para ignorar el charset
        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('Content-Type')
        );
    }

    public function test_doctor_can_complete_medical_exam_review(): void
    {
        $this->actingAs($this->doctor);

        $exam = MedicalExam::create([
            'pet_id' => $this->pet->id,
            'medical_record_id' => $this->medicalRecord->id,
            'uploaded_by' => $this->doctor->id,
            'title' => 'Radiografia',
            'file_path' => 'dummy',
            'original_name' => 'dummy.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_at' => now(),
        ]);

        $this->assertNull($exam->reviewed_by_doctor_at);

        $response = $this->post(route('medical_exams.complete_review', $exam));

        $response->assertRedirect();

        $exam->refresh();
        $this->assertNotNull($exam->reviewed_by_doctor_at);
        $this->assertEquals($this->doctor->id, $exam->reviewed_by_doctor_id);
    }

    public function test_non_doctor_cannot_complete_review(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $this->actingAs($client);

        $exam = MedicalExam::create([
            'pet_id' => $this->pet->id,
            'medical_record_id' => $this->medicalRecord->id,
            'uploaded_by' => $client->id,
            'title' => 'Radiografia',
            'file_path' => 'dummy',
            'original_name' => 'dummy.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_at' => now(),
        ]);

        $response = $this->post(route('medical_exams.complete_review', $exam));
        $response->assertStatus(403);
    }

    public function test_doctor_cannot_complete_already_reviewed_exam(): void
    {
        $this->actingAs($this->doctor);

        $exam = MedicalExam::create([
            'pet_id' => $this->pet->id,
            'medical_record_id' => $this->medicalRecord->id,
            'uploaded_by' => $this->doctor->id,
            'title' => 'Radiografia',
            'file_path' => 'dummy',
            'original_name' => 'dummy.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_at' => now(),
            'reviewed_by_doctor_at' => now(),
            'reviewed_by_doctor_id' => $this->doctor->id,
        ]);

        $response = $this->post(route('medical_exams.complete_review', $exam));
        $response->assertRedirect();
        $response->assertSessionHas('info', 'Este examen ya había sido revisado.');
    }

    /**
     * Test de descarga (Download) - Para subir cobertura
     */
    public function test_doctor_can_download_medical_exam(): void
    {
        $this->actingAs($this->doctor);

        $fileName = 'descarga.pdf';
        $fullPath = 'medical_exams/pet_'.$this->pet->id.'/'.$fileName;
        Storage::disk('local')->put($fullPath, 'Contenido');

        $exam = MedicalExam::create([
            'pet_id' => $this->pet->id,
            'uploaded_by' => $this->doctor->id,
            'title' => 'Descarga',
            'file_path' => $fullPath,
            'original_name' => $fileName,
            'mime_type' => 'application/pdf',
            'file_size' => 500,
            'uploaded_at' => now(),
        ]);

        $response = $this->get(route('medical_exams.download', $exam));

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename=descarga.pdf');
    }

    public function test_view_and_download_return_404_if_file_missing(): void
    {
        $this->actingAs($this->doctor);

        $exam = MedicalExam::create([
            'pet_id' => $this->pet->id,
            'uploaded_by' => $this->doctor->id,
            'title' => 'Missing File',
            'file_path' => 'medical_exams/does_not_exist.pdf',
            'original_name' => 'missing.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 500,
            'uploaded_at' => now(),
        ]);

        $this->get(route('medical_exams.view', $exam))->assertStatus(404);
        $this->get(route('medical_exams.download', $exam))->assertStatus(404);
    }

    public function test_client_cannot_upload_to_other_pets(): void
    {
        $otherClient = User::factory()->create(['role' => 'client']);
        $otherPet = Pet::factory()->create(['user_id' => $otherClient->id]);

        $maliciousClient = User::factory()->create(['role' => 'client']);
        $this->actingAs($maliciousClient);

        $file = UploadedFile::fake()->create('hack.pdf', 100);

        $response = $this->post(route('medical_exams.store', $otherPet), [
            'files' => [$file],
        ]);

        $response->assertStatus(403);
    }

    public function test_client_can_edit_their_own_uploaded_medical_exam(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $client->id]);
        $this->actingAs($client);

        // 1. Create a dummy file physically in local storage
        $originalFile = 'original.pdf';
        $originalPath = 'medical_exams/pet_'.$pet->id.'/'.$originalFile;
        Storage::disk('local')->put($originalPath, 'Original Content');

        // 2. Create the medical exam record
        $exam = MedicalExam::create([
            'pet_id' => $pet->id,
            'uploaded_by' => $client->id,
            'title' => 'Examen Original',
            'category' => 'Original',
            'file_path' => $originalPath,
            'original_name' => $originalFile,
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_at' => now(),
        ]);

        // Verify we can access the edit page
        $editResponse = $this->get(route('medical_exams.edit', $exam));
        $editResponse->assertOk();
        $editResponse->assertSee('Examen Original');

        // Create a new fake file to replace the old one
        $newFile = UploadedFile::fake()->create('actualizado.pdf', 500);

        // Update the exam
        $updateResponse = $this->put(route('medical_exams.update', $exam), [
            'title' => 'Examen Modificado',
            'category' => 'Modificado',
            'exam_date' => now()->toDateString(),
            'description' => 'Descripcion modificada',
            'file' => $newFile,
        ]);

        $updateResponse->assertRedirect(route('pets.exams', ['pet_id' => $pet->id]));

        // Check database
        $this->assertDatabaseHas('medical_exams', [
            'id' => $exam->id,
            'title' => 'Examen Modificado',
            'category' => 'Modificado',
            'description' => 'Descripcion modificada',
            'original_name' => 'actualizado.pdf',
        ]);

        // Check storage has new file and old file was deleted
        $exam->refresh();
        $this->assertTrue(Storage::disk('local')->exists($exam->file_path));
        $this->assertFalse(Storage::disk('local')->exists($originalPath));
    }

    public function test_client_cannot_edit_exams_uploaded_by_doctors(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $client = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $client->id]);

        $exam = MedicalExam::create([
            'pet_id' => $pet->id,
            'uploaded_by' => $doctor->id,
            'title' => 'Examen Doctor',
            'category' => 'Clinico',
            'file_path' => 'dummy_doctor.pdf',
            'original_name' => 'doctor.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_at' => now(),
        ]);

        // Client logs in and tries to edit the doctor's uploaded exam
        $this->actingAs($client);

        $responseGet = $this->get(route('medical_exams.edit', $exam));
        $responseGet->assertStatus(403);

        $responsePut = $this->put(route('medical_exams.update', $exam), [
            'title' => 'Hack Attempt',
        ]);
        $responsePut->assertStatus(403);
    }

    public function test_only_order_doctor_gets_assigned_unreviewed_exam_task(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $doctorA = User::factory()->create(['role' => 'doctor']);
        $doctorB = User::factory()->create(['role' => 'doctor']);
        $client = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Kira']);

        // Create an order associated with Doctor A
        $order = MedicalOrder::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctorA->id,
            'order_date' => now()->toDateString(),
            'order_type' => 'Laboratorio',
            'description' => 'Examen de Kira',
            'status' => 'pending',
        ]);

        // Client uploads an exam associated with that order
        $exam = MedicalExam::create([
            'pet_id' => $pet->id,
            'medical_order_id' => $order->id,
            'uploaded_by' => $client->id,
            'title' => 'Examen Kira',
            'file_path' => 'kira.pdf',
            'original_name' => 'kira.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_at' => now(),
        ]);

        // Log in as admin and sync/trigger automatic tasks by loading index
        $this->actingAs($admin);
        $response = $this->get(route('tasks.index'));
        $response->assertOk();

        // Verify task exists for Doctor A
        $this->assertDatabaseHas('doctor_tasks', [
            'doctor_id' => $doctorA->id,
            'source_id' => $exam->id,
            'source_type' => 'medical_exam',
        ]);

        // Verify task DOES NOT exist for Doctor B
        $this->assertDatabaseMissing('doctor_tasks', [
            'doctor_id' => $doctorB->id,
            'source_id' => $exam->id,
            'source_type' => 'medical_exam',
        ]);
    }

    public function test_only_order_doctor_sees_pending_exam_on_dashboard(): void
    {
        $doctorA = User::factory()->create(['role' => 'doctor']);
        $doctorB = User::factory()->create(['role' => 'doctor']);
        $client = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Kira']);

        // Create an order associated with Doctor A
        $order = MedicalOrder::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctorA->id,
            'order_date' => now()->toDateString(),
            'order_type' => 'Laboratorio',
            'description' => 'Examen de Kira',
            'status' => 'pending',
        ]);

        // Client uploads an exam associated with that order
        $exam = MedicalExam::create([
            'pet_id' => $pet->id,
            'medical_order_id' => $order->id,
            'uploaded_by' => $client->id,
            'title' => 'Hemograma Especial Kira',
            'file_path' => 'kira.pdf',
            'original_name' => 'kira.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_at' => now(),
        ]);

        // Doctor A logs in and loads dashboard
        $this->actingAs($doctorA);
        $responseA = $this->get(route('dashboard.index'));
        $responseA->assertOk();
        $responseA->assertSee('Hemograma Especial Kira');

        // Doctor B logs in and loads dashboard
        $this->actingAs($doctorB);
        $responseB = $this->get(route('dashboard.index'));
        $responseB->assertOk();
        $responseB->assertDontSee('Hemograma Especial Kira');
    }
}
